<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DuplicateContactRepository")
 */
class DuplicateContact
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
    private $salesforce_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mobile_phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $owner_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $stage;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $legacy_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lead_status;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMobilePhone(): ?string
    {
        return $this->mobile_phone;
    }

    public function setMobilePhone(?string $mobile_phone): self
    {
        $this->mobile_phone = $mobile_phone;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStage(): ?string
    {
        return $this->stage;
    }

    public function setStage(?string $stage): self
    {
        $this->stage = $stage;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getLegacyId(): ?string
    {
        return $this->legacy_id;
    }

    public function setLegacyId(?string $legacy_id): self
    {
        $this->legacy_id = $legacy_id;

        return $this;
    }

    public function getLeadStatus(): ?string
    {
        return $this->lead_status;
    }

    public function setLeadStatus(?string $lead_status): self
    {
        $this->lead_status = $lead_status;

        return $this;
    }
}
