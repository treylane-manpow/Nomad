<?php

namespace App\Command;

use App\Entity\Deal;
use App\Entity\LeadSource;
use App\Entity\User;
use App\Services\Mapper;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RetrieveLegacyCommand extends Command
{
    protected static $defaultName = 'retrieve';
    private $connection;
    private $em;
    public function __construct(string $name = null, Connection $connection, EntityManagerInterface $em)
    {
        parent::__construct($name);
        $this->connection = $connection;
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('object', InputArgument::OPTIONAL, 'Argument description')
        ;
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $node = (object)Mapper::get()[$input->getArgument('object')] ?? null;
        if($node == null)
            throw new \Exception('No map for that object');

        $stmt = $this->connection->prepare(file_get_contents('src/Scripts/' . $node->query));
        $stmt->execute();
        $x = 0;
        foreach($stmt->fetchAll() as $record)
        {
            $rec = new $node->type();
            $rec->populateFromQuery($record);
            $this->em->persist($rec);
            if($x++ % 100 == 0)
                $this->em->flush();
        }
        $this->em->flush();

        return 0;
    }
}
