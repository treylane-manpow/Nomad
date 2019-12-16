<?php

namespace App\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DisclosedInvestorRepository")
 */
class DisclosedInvestor
{
    private $hooks = [
        'owner' =>  [
            'type'  =>  User::class,
            'field' =>  'email'
        ],
        'Lead Source'   =>  [
            'type'  =>  LeadSource::class,
            'field' =>  'legacy_id',
        ],
        'Drill Down 1'  =>  [
            'type'  =>  LeadSource::class,
            'field' =>  'legacy_id',
        ],
        'Drill Down 2'  =>[
            'type'  =>  LeadSource::class,
            'field' =>  'legacy_id'
        ]
    ];

    public function update(EntityManagerInterface $em)
    {
        $tmp = $this->transform($em);
        unset($tmp['LeadSource']);
        unset($tmp['Drill_Down_1__c']);
        unset($tmp['Drill_Down_2__c']);
        unset($tmp['Investor_Stage__c']);
        unset($tmp['Stage__c']);
        unset($tmp['ContactType__c']);
        unset($tmp['Legacy_Homeland_ID__c']);
        unset($tmp['Drill_Down_1__c']);
        unset($tmp['CreatedDate']);
        return $tmp;
    }
    public function transform(EntityManagerInterface $em = null)
    {
        $tmp = json_decode($this->getPayload());
        foreach($this->hooks as $key => $hook)
        {
            $repo = $em->getRepository($hook['type']);
            $obj = $repo->findOneBy([
                $hook['field'] => $this->get($key)
            ]);
            if($obj == null)
                $tmp->$key = null;
            else
                $tmp->$key = $obj->getSalesforceId();
        }
        $tmp->OwnerId = $tmp->owner;
        unset($tmp->owner);
        $tmp->MailingStreet = $tmp->address1;
        unset($tmp->address1);
        $tmp->MailingCity = $tmp->city;
        unset($tmp->city);
        $tmp->MailingState = $tmp->state_code;
        unset($tmp->address2);
        unset($tmp->state_code);
        $tmp->MailingPostalCode = $tmp->zip;
        unset($tmp->zip);
        unset($tmp->country);
        $tmp->Description = $tmp->additional_notes;
        unset($tmp->additional_notes);
        $tmp->CreatedDate = $tmp->date_created;
        unset($tmp->date_created);
        foreach($tmp as $key => $val){
            if(($this->schema($key))->type == 'boolean')
            {
                $tmp->$key = (integer) $tmp->$key;
            }

            if(($this->schema($key))->type == 'datetime')
            {
                $date = new \DateTime($tmp->$key);
                $tmp->$key = $date->format('Y-m-d\TH:i:s');
            }
            if($key != ($this->schema($key))->name)
            {
                $tmp->{($this->schema($key))->name} = $tmp->$key;
                unset($tmp->$key);
            }
        }
        $this->transformed = json_encode($tmp);

        return (array)$tmp;
    }
    private $json;
    public function get(string $key){
        if($this->json == null){
            $this->json = json_decode($this->getPayload());
        }
        if($key == 'legacy_id')
            return $this->legacy_id;
        return $this->json->$key ?? null;
    }
    private $_schema;
    public function schema(string $key)
    {
        if($this->_schema == null)
            $this->_schema = json_decode(file_get_contents('src/schema/Contact.json'));
        foreach($this->_schema as $node)
        {
            if($node->name == $key || $node->label == $key)
                return $node;
        }
        throw new \Exception('Couldnt find: ' . $key);
    }
    public function populateFromQuery($prop) :self
    {
        try{
            $this->payload   = json_encode($prop);
            $this->legacy_id = $prop["Legacy Homeland ID"];
            return $this;
        }catch(\Exception $e)
        {
            dd($prop);
        }

    }


    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $payload;

    /**
     * @ORM\Column(type="integer")
     */
    private $legacy_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $salesforce_id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $response;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $transformed;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $updated;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPayload(): ?string
    {
        return $this->payload;
    }

    public function setPayload(string $payload): self
    {
        $this->payload = $payload;

        return $this;
    }

    public function getLegacyId(): ?int
    {
        return $this->legacy_id;
    }

    public function setLegacyId(int $legacy_id): self
    {
        $this->legacy_id = $legacy_id;

        return $this;
    }

    public function getSalesforceId(): ?string
    {
        return $this->salesforce_id;
    }

    public function setSalesforceId(?string $salesforce_id): self
    {
        $this->salesforce_id = $salesforce_id;

        return $this;
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function setResponse(?string $response): self
    {
        $this->response = $response;

        return $this;
    }

    public function getTransformed(): ?string
    {
        return $this->transformed;
    }

    public function setTransformed(?string $transformed): self
    {
        $this->transformed = $transformed;

        return $this;
    }

    public function setUpdated(?bool $updated): self
    {
        $this->updated = $updated;

        return $this;
    }
}
