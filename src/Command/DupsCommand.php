<?php

namespace App\Command;

use App\Apis\Salesforce\SalesforceApi;
use App\Entity\Deal;
use App\Entity\Duplicate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DupsCommand extends Command
{
    protected static $defaultName = 'doop';

    private $api;
    private $em;
    public function __construct(string $name = null, SalesforceApi $api, EntityManagerInterface $em)
    {
        parent::__construct($name);
        $this->em = $em;
        $this->api = $api;
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $stmt = $this->em->getConnection()->prepare("SELECT salesforce_id, property_stage, is_active FROM Duplicate where is_active = 0 and property_stage in ('New', 'Inspected', 'Offer Sent', 'CMA', 'Approved To Open Title', 'Available' ) ");
                $stmt->execute();
        $x = 0;
        foreach($stmt->fetchAll() as $row){
            $row = (object)$row;

                if($x++ < -1)
                    continue;

                $response = $this->api->updateObject('pba__listing__c', $row->salesforce_id, [
                   'Active__c'  =>  1
                ]);
                echo "[$x++] updated: " . $row->salesforce_id . "\n";
                $response = $response->getBody();
                $response = json_decode($response);
        }
exit;

/*
        $repo = $this->em->getRepository(Duplicate::class);
$deals = $this->em->getRepository(Deal::class);
        $stmt = $this->em->getConnection()->prepare("SELECT street, city,Property_Stage__c, IsActive, zip_code, owner_id, count(*) as 'count' FROM duplicate where street is not null  group by street, city, zip_code, owner_id ");
        $stmt->execute();
$x = 0;
        foreach($stmt->fetchAll() as $row){
            $row = (object)$row;
            if($row->count == 2)
            {
                if($x++ < 2)
                    continue;
                $result = $repo->findBy([
                    'street'    =>  $row->street,
                    'city'      =>  $row->city,
                    'zip_code'  =>  $row->zip_code,
                    'owner_id'  =>  $row->owner_id,

                ]);
                $delete = $result[0];
                $bad = $deals->findOneBy([
                    'salesforce_id' =>  $delete->getSalesforceId()
                ]);
                if($bad != null){
                    $delete = $result[1];
                }
                echo "deleteing: " . $delete->getSalesforceId() . "\n";
                $this->api->delete('pba__listing__c', $delete->getSalesforceId());

            }
        }





        exit;
*/
        $results = $this->api->query("SELECT Id, OwnerId, Name,Property_Stage__c, Active__c, pba__Address_pb__c, pba__City_pb__c, pba__PostalCode_pb__c FROM pba__listing__c where Office__r.Name = 'Oklahoma City'");
$x= 0;
        while(isset($results->nextRecordsUrl)){
            foreach($results->records as $record){

                $doop  = (new Duplicate())->setSalesforceId($record->Id)
                    ->setName($record->Name)
                    ->setZipCode($record->pba__PostalCode_pb__c)
                    ->setCity($record->pba__City_pb__c)
                    ->setStreet($record->pba__Address_pb__c)
                    ->setOwnerId($record->OwnerId)
                    ->setPropertyStage($record->Property_Stage__c)
                    ->setIsActive($record->Active__c)
                ;
                $this->em->persist($doop);
                if($x++ % 100 == 0){
                    $this->em->flush();
                }
            }
            $this->em->flush();
            $results = $this->api->nextUrl($results->nextRecordsUrl);
        }
        foreach($results->records as $record){

            $doop  = (new Duplicate())->setSalesforceId($record->Id)
                ->setName($record->Name)
                ->setZipCode($record->pba__PostalCode_pb__c)
                ->setCity($record->pba__City_pb__c)
                ->setStreet($record->pba__Address_pb__c)
                ->setOwnerId($record->OwnerId)
                ->setPropertyStage($record->Property_Stage__c)
                ->setIsActive($record->Active__c)
            ;
            $this->em->persist($doop);
            if($x++ % 100 == 0){
                $this->em->flush();
            }
        }
        $this->em->flush();
        return 0;
    }
}
