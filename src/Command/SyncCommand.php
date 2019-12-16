<?php

namespace App\Command;

use App\Apis\Salesforce\SalesforceApi;
use App\Services\Mapper;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SyncCommand extends Command
{
    protected static $defaultName = 'sync';

    private $connection;
    private $em;
    private $api;
    public function __construct(string $name = null, Connection $connection, EntityManagerInterface $em, SalesforceApi $api)
    {
        parent::__construct($name);
        $this->connection = $connection;
        $this->em = $em;
        $this->api = $api;
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

        $repo = $this->em->getRepository($node->type);

        $results = $repo->findBy(['salesforce_id' => null]);

        $x=0;
        foreach($results as $record)
        {
            echo "Syncing: " . $record->get('legacy_id') . "\n";
        //    echo 'SELECT Id, Legacy_Homeland_Id__c FROM ' . $node->salesforce_name . " where Legacy_Homeland_ID__c = '".$record->get('legacy_id')."'";exit;
            $sf = $this->api->query('SELECT Id, Legacy_Homeland_Id__c FROM ' . $node->salesforce_name . " where Legacy_Homeland_ID__c = '".$record->get('legacy_id')."'");
            foreach($sf->records as $rec){
                $record->setSalesforceId($rec->Id);
                $this->em->persist($record);
                break;
            }
            if($x++ % 100 == 0)
                $this->em->flush();
        }
        $this->em->flush();
        return 0;
    }
}
