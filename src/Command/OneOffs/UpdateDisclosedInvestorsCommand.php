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

class UpdateDisclosedInvestorsCommand extends Command
{
    protected static $defaultName = 'update:disclosed';
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
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $node = (object)Mapper::get()['investor'] ?? null;
        if($node == null)
            throw new \Exception('No map for that object');

        $repo = $this->em->getRepository($node->type);

        $results = $repo->findBy(['updated' => null]);

        $x=0;
        foreach($results as $record)
        {
            echo "Syncing: " . $record->get('legacy_id') . "\n";
            //    echo 'SELECT Id, Legacy_Homeland_Id__c FROM ' . $node->salesforce_name . " where Legacy_Homeland_ID__c = '".$record->get('legacy_id')."'";exit;
            $this->api->updateObject('Contact', $record->getSalesforceId(), $record->update($this->em));
            $record->setUpdated(true);
            $this->em->persist($record);
            $this->em->flush();
        }

        return 0;
    }
}
