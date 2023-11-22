<?php


namespace App\Websockets;

use App\Entity\PublisherDescription;
use App\Services\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Ratchet\ConnectionInterface;
use Redis;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Serializer;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;

class SensorUploadMessageHandler implements MessageComponentInterface
{
    protected $connections;

    private $redis;

    private Serializer $serializer;

    private EntityManagerInterface $em;

    private OutputInterface $output;

    public function __construct(EntityManagerInterface $em, OutputInterface $output)
    {
        $this->connections = new SplObjectStorage;
        $this->redis = new Redis();
        $this->redis->connect("redis");
        $this->serializer = new Serializer();
        $this->em = $em;
        $this->output = $output;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->connections->attach($conn);
    }

    public function onMessage
    (
        ConnectionInterface $from,
        $msg
    )
    {
        $msg = json_decode($msg, true);
        $id = [];
        $data = [];
        foreach ($msg['data'] as &$item) {
            $id[] = $item['id'];
            $data[$item['id']] = ['id' => $item['id'], 'value' => $item['value'], 'updated' => time()];
        }
        $validationSettings = $this->em->getRepository(PublisherDescription::class)->getSensorsSettings($id, 'validation');
        foreach ($validationSettings as $itemSetting) {
            $itemSettingArray = $itemSetting->getAsArray();
            if ($itemSettingArray['alias'] === 'rd') {
                continue;
            }
            $data[$itemSettingArray['id']]['validation'][] = $itemSettingArray;
        }
        foreach ($data as $publisherArray) {
            if (isset($publisherArray['validation'])) {
                foreach ($publisherArray['validation'] as $key => $validation) {
                    $aliasMethod = $validation['alias'];
                    $status = (new Validator)->$aliasMethod($validation['value'], $publisherArray['value']);
                    if (!$status) {
                        foreach ($this->connections as $connection) {
                            if ($connection === $from) {
                                $connection->send(json_encode(['msg' => 'call to function']));
                                break;
                            }
                        }
                    }
                    $publisherArray['validation'][$key] = ['isOk' => $status];
                }
            }
            $this->output->writeln(json_encode($publisherArray));
            $this->redis->hSet('sensor', $publisherArray['id'], json_encode($publisherArray));
        }

    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->connections->detach($conn);
    }

    public function onError(ConnectionInterface $conn, Exception $e)
    {
        $this->connections->detach($conn);
        $conn->close();
    }
}