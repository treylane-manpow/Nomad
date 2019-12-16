<?php

namespace App\Command;

use App\Apis\Salesforce\SalesforceApi;
use App\Entity\Duplicate;
use App\Entity\DuplicateContact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DedupliateContactsCommand extends Command
{
    protected static $defaultName = 'doop:contacts';
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
            ->addArgument('switch', InputArgument::OPTIONAL, 'Argument description')

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        switch($input->getArgument('switch'))
        {
            case 1:
                $results = $this->api->query("SELECT Id, OwnerId, Name, Email, Phone, MobilePhone, Stage__c, LastModifiedDate, Legacy_Homeland_ID__c, Lead_Status__c FROM Contact where Office__r.Name = 'Oklahoma City'");
                $x= 0;
                while(isset($results->nextRecordsUrl)){
                    foreach($results->records as $record){

                        $doop = (new DuplicateContact())->setOwnerId($record->OwnerId)
                                        ->setSalesforceId($record->Id)
                                        ->setEmail($record->Email)
                                        ->setMobilePhone($record->MobilePhone)
                                        ->setPhone($record->Phone)
                                        ->setName($record->Name)
                                        ->setStage($record->Stage__c)
                                        ->setUpdatedAt(new \DateTime($record->LastModifiedDate))
                                        ->setLegacyId($record->Legacy_Homeland_ID__c)
                                        ->setLeadStatus($record->Lead_Status__c)
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
                    $doop = (new DuplicateContact())->setOwnerId($record->OwnerId)
                        ->setSalesforceId($record->Id)
                        ->setEmail($record->Email)
                        ->setMobilePhone($record->MobilePhone)
                        ->setPhone($record->Phone)
                        ->setName($record->Name)
                        ->setStage($record->Stage__c)
                        ->setUpdatedAt(new \DateTime($record->LastModifiedDate))
                        ->setLegacyId($record->Legacy_Homeland_ID__c)
                        ->setLeadStatus($record->Lead_Status__c)
                    ;
                    $this->em->persist($doop);
                    if($x++ % 100 == 0){
                        $this->em->flush();
                    }
                }
                $this->em->flush();


                break;
            case 2:

 $repo = $this->em->getRepository(DuplicateContact::class);
        $stmt = $this->em->getConnection()->prepare("SELECT mobile_phone, phone, email, owner_id, name, stage, lead_status, count(*) as 'count' from duplicate_contact  group by mobile_phone, phone, email, owner_id, name, stage, lead_status");
        $stmt->execute();
$x = 0;
        foreach($stmt->fetchAll() as $row){
            $row = (object)$row;

            if($row->count == 4)
            {
                if($x++ < 3)
                    continue;
                if($row->stage == 'Disclosed Investor' || $row->lead_status == 'Disclosed Investor')
                    continue;
                $result = $repo->findBy([
                    'mobile_phone'  =>  $row->mobile_phone,
                    'phone'         =>  $row->phone,
                    'email'         =>  $row->email,
                    'owner_id'      =>  $row->owner_id,
                    'name'          =>  $row->name,
                    'stage'         =>  $row->stage,
                    'lead_status'   =>  $row->lead_status
                ], [
                    'updated_at' => 'desc'
                ]);

                $delete = $result[1];
                echo $x ."] deleteing : " . $delete->getSalesforceId() . "\n";
               echo "SELECT Id FROM Contact_Summary__c WHERE Contact__c = '".$delete->getSalesforceId()."'\n" ;
                $cs = $this->api->query("SELECT Id FROM Contact_Summary__c WHERE Contact__c = '".$delete->getSalesforceId()."'");

                $this->api->delete('Contact_Summary__c', $cs->records[0]->Id);
                $this->api->delete('Contact', $delete->getSalesforceId());
                $delete = $result[2];
                echo $x ."] deleteing : " . $delete->getSalesforceId() . "\n";
                echo "SELECT Id FROM Contact_Summary__c WHERE Contact__c = '".$delete->getSalesforceId()."'\n" ;
                $cs = $this->api->query("SELECT Id FROM Contact_Summary__c WHERE Contact__c = '".$delete->getSalesforceId()."'");

                $this->api->delete('Contact_Summary__c', $cs->records[0]->Id);
                $this->api->delete('Contact', $delete->getSalesforceId());
                $delete = $result[3];
                echo $x ."] deleteing : " . $delete->getSalesforceId() . "\n";
                echo "SELECT Id FROM Contact_Summary__c WHERE Contact__c = '".$delete->getSalesforceId()."'\n" ;
                $cs = $this->api->query("SELECT Id FROM Contact_Summary__c WHERE Contact__c = '".$delete->getSalesforceId()."'");

                $this->api->delete('Contact_Summary__c', $cs->records[0]->Id);
                $this->api->delete('Contact', $delete->getSalesforceId());

            }
        }

                break;
        }
        return 0;
    }
}
