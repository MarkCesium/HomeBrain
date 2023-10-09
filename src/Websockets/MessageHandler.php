<?php


namespace App\Websockets;

use DateTime;
use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;

class MessageHandler implements MessageComponentInterface
{
    protected $connections;

    public function __construct()
    {
        $this->connections = new SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->connections->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = [
            [
                'id' => 1,
                'value' => rand(0, 100),
                'name' => 'Lorem1',
                'updated' => (new DateTime('now'))->format('H:i:s d-m-Y')
            ],
            [
                'id' => 2,
                'value' => rand(0, 100),
                'name' => 'Lorem2',
                'updated' => (new DateTime('now'))->format('H:i:s d-m-Y')
            ],
            [
                'id' => 3,
                'value' => rand(0, 100),
                'name' => 'Lorem3',
                'updated' => (new DateTime('now'))->format('H:i:s d-m-Y')
            ],
            [
                'id' => 4,
                'value' => rand(0, 100),
                'name' => 'Lorem4',
                'updated' => (new DateTime('now'))->format('H:i:s d-m-Y')
            ],
            [
                'id' => 10,
                'value' => rand(0, 100),
                'name' => 'Lorem10',
                'updated' => (new DateTime('now'))->format('H:i:s d-m-Y')
            ],
            [
                'id' => 14,
                'value' => rand(0, 100),
                'name' => 'Lorem14',
                'updated' => (new DateTime('now'))->format('H:i:s d-m-Y')
            ],
            [
                'id' => 16,
                'value' => rand(0, 100),
                'name' => 'Lorem16',
                'updated' => (new DateTime('now'))->format('H:i:s d-m-Y')
            ]
        ];

        if (!rand(0, 10)) {
            $data[] = ['id' => 17, 'value' => rand(0, 100)];
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