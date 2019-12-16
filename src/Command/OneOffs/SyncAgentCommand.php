<?php

namespace App\Command\OneOffs;

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

class SyncAgentCommand extends Command
{
    protected static $defaultName = 'sync:agents';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
        ;
    }
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
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $node = (object)Mapper::get()['user'] ?? null;

        $repo = $this->em->getRepository($node->type);

        $results = $repo->findBy(['agent_id' => null]);

        $x=0;
        foreach($results as $record)
        {
            $sf = $this->api->query("SELECT Id, Agent_Name__c FROM Agent__c WHERE Agent_Name__c = '".$record->getSalesforceId()."'");
            foreach($sf->records as $rec){
                $record->setAgentId($rec->Id);
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
