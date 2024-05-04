<?php


namespace App\Websockets;

use App\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Redis;
use SplObjectStorage;
use Symfony\Component\Serializer\Serializer;

class DeviceMessageHandler implements MessageComponentInterface
{
    protected $connections;

    private $redis;

    private Serializer $serializer;

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->connections = new SplObjectStorage;
        $this->redis = new Redis();
        $this->redis->connect('localhost', 6379);
        $this->serializer = new Serializer();
        $this->em = $em;
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
//        $this->redis->rPush('hello', 'world');
        $locationId = intval(json_decode($msg));
        $publishers = $this->em->getRepository(Location::class)->getLocationPublishers($locationId);
        foreach ($this->connections as $connection) {
            if ($connection === $from) {
                $connection->send(json_encode(''));
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