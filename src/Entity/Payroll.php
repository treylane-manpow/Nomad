<?php

namespace App\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PayrollRepository")
 */
class Payroll
{


    private $hooks = [
        'Deal'  =>  [
            'type'  =>  Deal::class,
            'field' =>  'legacy_id',
            'required'  => true
        ],
        'Primary_AA__c'   =>  [
            'type'      =>  User::class,
            'required'  =>  true,
            'field' =>  'email'
        ],
        'Primary_DA__c'   =>  [
            'type'      =>  User::class,
            'required'  =>  false,
            'field' =>  'email'
        ],
        'Secondary_AA__c'   =>  [
            'type'      =>  User::class,
            'required'  =>  false,
            'field' =>  'email'
        ],
        'Secondary_DA__c'   =>  [
            'type'      =>  User::class,
            'required'  =>  false,
            'field' =>  'email'
        ],
        'Additional_AA__c'  =>  [
            'type'      =>  User::class,
            'required'  =>  false,
            'field' =>  'email'
        ],
        'Additional_DA__c'  =>  [
            'type'      =>  User::class,
            'required'  =>  false,
            'field' =>  'email'
        ],
        'Lead Source' => [
            'type'      =>  LeadSource::class,
            'required'  =>  true,
            'field' =>  'legacy_id'
        ],

    ];

    public function transform(EntityManagerInterface $em = null)
    {
        $tmp = json_decode($this->getPayload());

unset($tmp->{"Ownership Type"});
$tmp->Close_Date__c = $tmp->{"Disposition Close Date"};
unset($tmp->{"Disposition Close Date"});

  //      $tmp->Office__c = 'a0z1I0000016RmDQAU';
        foreach($this->hooks as $key => $hook) {
            $repo = $em->getRepository($hook['type']);
            $obj = $repo->findOneBy([
                $hook['field'] => $this->get($key)
            ]);
            if ($obj == null && $hook['required'] == false)
                $tmp->$key = null;
            else if ($obj == null && $hook['required'] == true)
                throw new \Exception('Need to find: ' . $key . '=> ' . $this->get($key), -5);
            else {

                if ($hook['type'] == User::class)
                    $tmp->$key = $obj->getAgentId();
                else
                    $tmp->$key = $obj->getSalesforceId();
            }

        }
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
            if($this->schema($key)->updateable == false && $key != 'CreatedDate' && $key != 'LastModifiedDate')
                unset($tmp->$key);
        }
        $tmp->Accounting_Office__c = 'a0z1I0000016RmDQAU';
        $this->output = json_encode($tmp);
        if($tmp->Deal__c == -1) {
            $this->salesforce_id = -1;
            throw new \Exception('Moneybug Deal', -1);
        }
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
            $this->_schema = json_decode(file_get_contents('src/schema/Payroll__c.json'));
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
            $this->payload = json_encode($prop);
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $output;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $salesforce_id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $response;

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

    public function getOutput(): ?string
    {
        return $this->output;
    }

    public function setOutput(?string $output): self
    {
        $this->output = $output;

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
}
