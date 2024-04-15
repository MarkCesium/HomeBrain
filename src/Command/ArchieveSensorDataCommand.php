<?php

namespace App\Command;

use App\Entity\Publisher;
use App\Entity\PublisherValueArchieve;
use Doctrine\ORM\EntityManagerInterface;
use Redis;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\VarDumper\VarDumper;

#[AsCommand(
    name: 'run:ws:sensor:update',
    description: 'Add a short description for your command',
)]
class ArchieveSensorDataCommand extends Command
{
    private $redis;

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
        $this->redis = new Redis();
        $this->redis->connect("localhost", 6379);
    }

    protected function configure(): void
    {
//        $this
//            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->success('Executed');
        $data = $this->redis->mget(
            $this->redis->keys('sensor:*')
        );
        foreach ($data as $key => &$item) {
            $archieveArray = json_decode($item, true);
            $publisher = $this->em->getRepository(Publisher::class)->findOneBy(['id' => $archieveArray['id']]);
            if (!$publisher) {
                $this->redis->del('sensor:'.$archieveArray['id']);
                continue;
            }
            $archieveArray['publisher'] = $publisher;
            $archieveArray['updated'] = (new \DateTime())->setTimestamp($archieveArray['updated']);
            $archieveValue = new PublisherValueArchieve($archieveArray);
            $this->em->persist($archieveValue);
        }
        $this->em->flush();
        $io->success('Done!');

        return Command::SUCCESS;
    }
}
