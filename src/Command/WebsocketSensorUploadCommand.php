<?php


namespace App\Command;

use App\Websockets\SensorMessageHandler;
use App\Websockets\SensorUploadMessageHandler;
use Doctrine\ORM\EntityManagerInterface;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WebsocketSensorUploadCommand extends Command
{
    protected static $defaultName = "run:ws:sensor:upload";

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em, string $name = null)
    {
        $this->em = $em;
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $port = 3003;
        $output->writeln("Starting server on port " . $port);
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new SensorUploadMessageHandler($this->em, $output)
                )
            ),
            $port
        );
        $server->run();

        return 0;
    }
}