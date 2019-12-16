<?php

namespace App\Command\OneOffs;

use App\Apis\Salesforce\SalesforceApi;
use App\Repository\LeadSourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MapLeadSourceCommand extends Command
{
    protected static $defaultName = 'map:lead-source';

    private $repo;
    private $api;
    private $em;
    public function __construct(string $name = null, LeadSourceRepository $repo, EntityManagerInterface $em, SalesforceApi $api )
    {
        parent::__construct($name);
        $this->repo = $repo;
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
        $values = [];
        foreach(explode("\n", file_get_contents('src/Scripts/leads.csv')) as $row)
        {
            $tokens = explode(',', $row);
            if(strpos($tokens[0], 'id') == false && $tokens[0] != '')
            {
                $ls = $this->repo->findOneBy(['legacy_id'  =>  $tokens[0]]);
                $tmp =   preg_replace('/[^0-9a-zA-Z ]/i', '', $tokens[2]);
                $tmp = str_replace("\n", '', $tmp);
                $results = $this->api->query("SELECT Id, Name from Lead_Source__c WHERE Name ='". $tmp . "'");
                foreach($results->records as $record)
                {
                    $ls->setSalesforceId($record->Id);
                    $this->em->persist($ls);
                    break;
                }
            }
        }
        $this->em->flush();


        return 0;
    }
}
