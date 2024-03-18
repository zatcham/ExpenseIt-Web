<?php

namespace App\Entity;

use App\Repository\XeroIntegrationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: XeroIntegrationRepository::class)]
class XeroIntegration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(length: 1500, nullable: true)]
    private ?string $access_token = null;

    #[ORM\Column(length: 450, nullable: true)]
    private ?string $refresh_token = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $last_updated = null;

    #[ORM\OneToOne(inversedBy: 'xeroIntegration', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Companies $company = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tenant_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccessToken(): ?string
    {
        return $this->access_token;
    }

    public function setAccessToken(?string $access_token): static
    {
        $this->access_token = $access_token;

        return $this;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refresh_token;
    }

    public function setRefreshToken(?string $refresh_token): static
    {
        $this->refresh_token = $refresh_token;

        return $this;
    }

    public function getLastUpdated(): ?\DateTimeInterface
    {
        return $this->last_updated;
    }

    public function setLastUpdated(\DateTimeInterface $last_updated): static
    {
        $this->last_updated = $last_updated;

        return $this;
    }

    public function getCompany(): ?Companies
    {
        return $this->company;
    }

    public function setCompany(Companies $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getTenantId(): ?string
    {
        return $this->tenant_id;
    }

    public function setTenantId(?string $tenant_id): static
    {
        $this->tenant_id = $tenant_id;

        return $this;
    }
}
