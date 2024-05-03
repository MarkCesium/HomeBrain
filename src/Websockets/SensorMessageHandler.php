<?php


namespace App\Websockets;

use App\Entity\Location;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Ratchet\ConnectionInterface;
use Redis;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Serializer;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;
use Symfony\Component\VarDumper\VarDumper;

class SensorMessageHandler implements MessageComponentInterface
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
        $msgData = json_decode($msg, true);
        if ($msgData['type'] === 'sendDevice') {
            $deviceID = 'device:' . $msgData['device'];
            $msg = [
                'status' => 'wait',
                'value' => $msgData['value'],
                'id' => $msgData['device']
            ];
            $this->redis->setex($deviceID, 60, json_encode($msg));
            return;
        } elseif ($msgData['type'] === 'getLocation') {
            $location = intval($msgData['location']);
        }
        $locationID = 'location:' . $location;
        $publishersID = $this->redis->get($locationID);
        if ($publishersID === false) {
            $publishers = $this->em->getRepository(Location::class)->getLocationPublishers($location);
            if (empty($publishers)) {
                return;
            }
            $publishersID = [];
            foreach ($publishers as $item) {
                if ($item->getType() === 2) $publishersID['sensors'][] = 'sensor:' . $item->getId();
                else $publishersID['devices'][] = 'device:' . $item->getId();
            }
            $this->redis->setex($locationID, 60, json_encode($publishersID));
            $sensorsValue = $this->redis->mget($publishersID['sensors']);
            $devicesValue = $this->redis->mget($publishersID['devices']);
        } else {
            $sensorsValue = $this->redis->mget(json_decode($publishersID, true)['sensors']);
            $devicesValue = $this->redis->mget(json_decode($publishersID, true)['devices']);
        }
        $data = [];
        foreach ($sensorsValue as $item) {
            $data['sensors'][] = json_decode($item);
        }
        foreach ($devicesValue as $item) {
            $data['devices'][] = json_decode($item);
        }
        foreach ($this->connections as $connection) {
            if ($connection === $from) {
                $msg = [
                    'type' => 'sendSensors',
                    'data' => $data
                ];
                $connection->send(json_encode($msg));
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