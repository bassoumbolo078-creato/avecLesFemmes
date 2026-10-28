<?php

namespace App\Trait;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Dates de création et de modification, partagées par les entités
 * (qui n'ont pas de lien d'héritage entre elles).
 * Les entités qui utilisent ce trait doivent porter #[ORM\HasLifecycleCallbacks].
 */
trait DateTimeTrait
{
    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTime $updateAt = null;

    #[ORM\PrePersist]
    public function initCreateAt(): void
    {
        $this->createAt ??= new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function initUpdateAt(): void
    {
        $this->updateAt = new \DateTime();
    }

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeImmutable $createAt): static
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getUpdateAt(): ?\DateTime
    {
        return $this->updateAt;
    }

    public function setUpdateAt(?\DateTime $updateAt): static
    {
        $this->updateAt = $updateAt;

        return $this;
    }
}