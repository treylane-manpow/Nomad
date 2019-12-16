<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OfficeRepository")
 */
class Office
{


    private $json;
    public function get(string $key){
        if($this->json == null){
            $this->json = json_decode($this->getPayload());
        }
        if($key == 'legacy_id')
            return $this->legacy_id;
        return $this->json->$key ?? null;
    }
    public function populateFromQuery($prop) :self
    {
        try{
            $this->payload = json_encode($prop);
            $this->legacy_id = $prop["id"];
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $response;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $output;

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

    public function getOutput(): ?string
    {
        return $this->output;
    }

    public function setOutput(?string $output): self
    {
        $this->output = $output;

        return $this;
    }
}
