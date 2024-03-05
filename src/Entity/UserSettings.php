<?php

namespace App\Entity;

use App\Repository\UserSettingsRepository;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSettingsRepository::class)]
#[Auditable]
class UserSettings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'userSettings', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?bool $NotifyOnAccept = null;

    #[ORM\Column(nullable: true)]
    private ?bool $NotifyOnUpdate = null;

    #[ORM\Column(nullable: true)]
    private ?bool $PushNotifs = null;

    #[ORM\Column(nullable: true)]
    private ?bool $MobileAccess = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getNotifyOnAccept(): ?bool
    {
        return $this->NotifyOnAccept;
    }

    public function setNotifyOnAccept(bool $NotifyOnAccept): static
    {
        $this->NotifyOnAccept = $NotifyOnAccept;

        return $this;
    }

    public function getNotifyOnUpdate(): ?bool
    {
        return $this->NotifyOnUpdate;
    }

    public function setNotifyOnUpdate(?bool $NotifyOnUpdate): static
    {
        $this->NotifyOnUpdate = $NotifyOnUpdate;

        return $this;
    }

    public function getPushNotifs(): ?bool
    {
        return $this->PushNotifs;
    }

    public function setPushNotifs(?bool $PushNotifs): static
    {
        $this->PushNotifs = $PushNotifs;

        return $this;
    }

    public function getMobileAccess(): ?bool
    {
        return $this->MobileAccess;
    }

    public function setMobileAccess(?bool $MobileAccess): static
    {
        $this->MobileAccess = $MobileAccess;

        return $this;
    }

}
