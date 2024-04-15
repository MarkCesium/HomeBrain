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
        $location = intval(json_decode($msg));
        $locationID = 'location:' . $location;
        $publishersID = $this->redis->get($locationID);
        if ($publishersID === false) {
            $publishers = $this->em->getRepository(Location::class)->getLocationPublishers($location);
            $publishersID = [];
            foreach ($publishers as $item) {
                $publishersID[] = 'sensor:'.$item->getId();
            }
            $this->redis->setex($locationID, 60, json_encode($publishersID));
            $publishersValue = $this->redis->mget($publishersID);
        } else {
            $publishersValue = $this->redis->mget(json_decode($publishersID));
        }
        $data = [];
        foreach ($publishersValue as $item) {
            $data[] = $item;
        }
        foreach ($this->connections as $connection) {
            if ($connection === $from) {
                $connection->send(json_encode($data));
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