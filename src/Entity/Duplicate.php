<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DuplicateRepository")
 */
class Duplicate
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $salesforce_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $street;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $zip_code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $owner_id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $IsActive;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $property_stage;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zip_code;
    }

    public function setZipCode(?string $zip_code): self
    {
        $this->zip_code = $zip_code;

        return $this;
    }

    public function getOwnerId(): ?string
    {
        return $this->owner_id;
    }

    public function setOwnerId(?string $owner_id): self
    {
        $this->owner_id = $owner_id;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->IsActive;
    }

    public function setIsActive(?bool $IsActive): self
    {
        $this->IsActive = $IsActive;

        return $this;
    }

    public function getPropertyStage(): ?string
    {
        return $this->property_stage;
    }

    public function setPropertyStage(?string $property_stage): self
    {
        $this->property_stage = $property_stage;

        return $this;
    }
}
