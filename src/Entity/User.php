<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    private $json;
    public function get(string $key){
        if($this->json == null){
            $this->json = json_decode($this->getPayload());
        }
        if($key == 'legacy_id')
            return $this->email;
        return $this->json->$key ?? null;
    }
    // @todo update this transformation for national
    public function transform(){
        $tmp = json_decode($this->getPayload());
        $tmp->Office__c = $tmp->name;
        $tmp->ProfileId = ($tmp->UserRoleId == 'disposition_agent') ? '	00e1I000000a3dJQAQ' : '	00e1I000000a3dJQAQ';
        $tmp->UserRoleId = '00E1I000000ESubUAG';

        unset($tmp->name);
        foreach($tmp as $key => $val){
            if(($this->schema($key))->type == 'boolean')
            {
                $tmp->$key = (integer) $tmp->$key;
            }
        }

        $tmp->TimeZoneSidKey = 'America/Chicago';
        $tmp->LanguageLocaleKey = 'en_US';
        $tmp->Alias = $tmp->FirstName[0] . substr($tmp->LastName, 0, '4');
        return (array)$tmp;
    }
    private $_schema;
    public function schema(string $key)
    {
        if($this->_schema == null)
            $this->_schema = json_decode(file_get_contents('src/schema/User.json'));
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
            $this->legacy_id = $prop["Legacy_Homeland_ID__c"];
            $this->email     =   $prop['Email'];

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
     * @ORM\Column(type="integer")
     */
    private $legacy_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $salesforce_id;

    /**
     * @ORM\Column(type="text")
     */
    private $payload;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $response;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $agent_id;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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

    public function getPayload(): ?string
    {
        return $this->payload;
    }

    public function setPayload(string $payload): self
    {
        $this->payload = $payload;

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

    public function getAgentId(): ?string
    {
        return $this->agent_id;
    }

    public function setAgentId(?string $agent_id): self
    {
        $this->agent_id = $agent_id;

        return $this;
    }
}
