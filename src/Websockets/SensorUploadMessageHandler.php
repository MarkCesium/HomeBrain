<?php


namespace App\Websockets;

use App\Entity\PublisherDescription;
use App\Services\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Redis;
use SplObjectStorage;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Serializer;

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
        $this->redis->connect('localhost', 6379);
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
        $sensorID = [];
        $data = [];
        if ($msg['type'] === 'sendDevice') {
            $id = 'device:' . $msg['data']['id'];
            $this->redis->set($id, json_encode(['status' => 'done', 'value' => $msg['data']['value'], 'id' => $msg['data']['id']]));
            return;
        }
        foreach ($msg['data']['sensors'] as $item) {
            $sensorID[] = $item['id'];
            $data[$item['id']] = ['id' => $item['id'], 'value' => $item['value'], 'updated' => time()];
        }
        $this->em->clear();
        $validationSettings = $this->em->getRepository(PublisherDescription::class)->getSensorsSettings($sensorID, 'validation');
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
                    $publisherArray['validation'][$key] = ['isOk' => $status];
                }
            }
            $this->redis->setex('sensor:' . $publisherArray['id'], 90, json_encode($publisherArray));
        }

        $deviceID = [];
        foreach ($msg['data']['devices'] as $item) {
            $deviceID[] = 'device:' . $item['id'];
        }
        $devices = $this->redis->mget($deviceID);
        foreach ($devices as $device) {
            if (!$device) {
                continue;
            }
            $device = json_decode($device, true);
            if ($device['status'] !== 'wait') {
                continue;
            }
            foreach ($this->connections as $connection) {
                if ($connection === $from) {
                    $connection->send(json_encode(['msg' => ['value' => $device['value'], 'device' => $device['id']]]));
                    $this->redis->del('device:' . $device['id']);
                    break;
                }
            }
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