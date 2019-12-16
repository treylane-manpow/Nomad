<?php

namespace App\Command;

use App\Apis\Salesforce\SalesforceApi;
use App\Services\Mapper;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PushCommand extends Command
{
    protected static $defaultName = 'push';
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
            ->addArgument('offset', InputArgument::OPTIONAL, 'Argument description')

            ->addOption('flipped')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $node = (object)Mapper::get()[$input->getArgument('object')] ?? null;
        if($node == null)
            throw new \Exception('No map for that object');

        $repo = $this->em->getRepository($node->type);

        $results = $repo->findBy(['salesforce_id' => null]);
        if($input->hasOption('flipped'))
            $results = array_reverse($results);
        $x=0;
        $offset = 0;
        if($input->hasArgument('offset'))
            $offset = $input->getArgument('offset');

        foreach($results as $record)
        {
            if($x++ < $offset)
                continue;
            echo $record->get('legacy_id') . "\n";

            try{
                $response = $this->api->insertObject($node->salesforce_name, $record->transform($this->em));
                $response = $response->getBody();
                $response = json_decode($response);
                $record->setSalesforceId($response->id);
                $this->em->persist($record);
                $this->em->flush();
//                sleep(1);
            } catch(\Exception $e)
            {
                if($e->getCode() == -1)
                {
                    $record->setSalesforceId(-1);
                    $this->em->persist($record);
                    $this->em->flush();
                    continue;

                }
                if($e->getCode() == -5)
                {
                    if($record->get('OwnerId') == null)
                    {
                        $record->setSalesforceId(-2);
                        $this->em->persist($record);
                        $this->em->flush();
                        continue;
                    }
                }
                echo $e->getMessage() ;
                dd($record->transform($this->em));
                exit;
                $record->setResponse($e->getMessage());
                $this->em->persist($record);
                $this->em->flush();
            }
        }
        return 0;
    }
}


/*
 *    if(strpos($record->getOutput(), 'Need to find: OwnerId') !== false)
            {
                $record->setSalesforceId(-2);
                $this->em->persist($record);
                $this->em->flush();
                continue;
            }
  //          echo  "Select Id,Legacy_Homeland_ID__c, Property_Stage__c, Name FROM pba__Listing__c WHERE pba__Street_pb__c = '".$record->get('Street')."' AND pba__City_pb__c = '".$record->get('City')."'  AND pba__PostalCode_pb__c = '".$record->get('Zip/Postal Code')."'";

            $tmp = $this->api->query("Select Id,Legacy_Homeland_ID__c, Property_Stage__c, Name, Lead_Source__c FROM pba__Listing__c WHERE pba__Address_pb__c = '".trim($record->get('Street'), "\n ")."' AND pba__City_pb__c = '".$record->get('City')."'  AND pba__PostalCode_pb__c LIKE '".$record->get('Zip/Postal Code')."%'");
            foreach($tmp->records as $_record)
            {
                $record->setSalesforceId($_record->Id);
                if($_record->Lead_Source__c == 'a1L1I000002qqOFUAY'){
                    continue;
                }
                if($record->get('legacy_id') != $_record->Legacy_Homeland_ID__c)
                {
//                    dd( $_record);
                    $dup = $repo->findOneBy(['legacy_id' => $_record->Legacy_Homeland_ID__c]);
                    $dupDate = new \DateTime($dup->get('LastModifiedDate'));
                    $tmpDate = new \DateTime($record->get('LastModifiedDate'));
                    if($tmpDate > $dupDate)
                    {
                        try{
                            $this->api->updateObject('pba__listing__c', $record->getSalesforceId(),
                                $record->update($this->em));
                        } catch(\Exception $e){
                            if($e->getCode() == -5)
                            {
                                if($record->get('OwnerId') == null)
                                {
                                    $record->setSalesforceId(-2);
                                    $this->em->persist($record);
                                    $this->em->flush();
                                    continue;
                                }
                            }
                            echo $e->getMessage() ;
                            dd($record->transform($this->em));
                            exit;
                        }
                    }
                    $record->setSalesforceId($_record->Legacy_Homeland_ID__c);

                    $this->em->persist($record);
                    $this->em->flush();
                    break;
                }
            }
            if($record->getSalesforceId() != null)
                continue;
 */