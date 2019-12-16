<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LeadSourceRepository")
 */
class LeadSource
{

    public function populateFromQuery($prop) :self
    {
        try{
            $this->payload = json_encode($prop);
            $this->legacy_id = $prop["id"];
            $this->legacy_name = $prop['name'];

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
    private $legacy_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $salesforce_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
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

    public function getLegacyName(): ?string
    {
        return $this->legacy_name;
    }

    public function setLegacyName(?string $legacy_name): self
    {
        $this->legacy_name = $legacy_name;

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
