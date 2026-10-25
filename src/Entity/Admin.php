<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Administrateur d'un groupement (un par groupement). C'est aussi une membre.
 */
#[ORM\Entity(repositoryClass: AdminRepository::class)]
class Admin extends User
{
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateNomination = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateFinMandat = null;

    public function __construct()
    {
        $this->dateNomination = new \DateTime();
    }

    public function getDateNomination(): ?\DateTimeInterface
    {
        return $this->dateNomination;
    }

    public function setDateNomination(\DateTimeInterface $dateNomination): static
    {
        $this->dateNomination = $dateNomination;

        return $this;
    }

    public function getDateFinMandat(): ?\DateTimeInterface
    {
        return $this->dateFinMandat;
    }

    public function setDateFinMandat(?\DateTimeInterface $dateFinMandat): static
    {
        $this->dateFinMandat = $dateFinMandat;

        return $this;
    }

    public function getRoles(): array
    {
        return array_unique([...parent::getRoles(), 'ROLE_ADMIN']);
    }
}