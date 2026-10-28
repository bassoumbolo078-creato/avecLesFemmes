<?php

namespace App\Entity;

use App\Repository\AmendeRepository;
use App\Trait\DateTimeTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Amende d'une membre absente qui n'a pas payé :
 * nombre de jours d'absence × amende journalière de la campagne.
 */
#[ORM\Entity(repositoryClass: AmendeRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Amende
{
    use DateTimeTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $nombreJours = null;

    #[ORM\Column]
    private ?int $montant = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Seance $seance = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ModeCotisation $modeCotisation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombreJours(): ?int
    {
        return $this->nombreJours;
    }

    public function setNombreJours(int $nombreJours): static
    {
        $this->nombreJours = $nombreJours;

        return $this;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getSeance(): ?Seance
    {
        return $this->seance;
    }

    public function setSeance(?Seance $seance): static
    {
        $this->seance = $seance;

        return $this;
    }

    public function getModeCotisation(): ?ModeCotisation
    {
        return $this->modeCotisation;
    }

    public function setModeCotisation(?ModeCotisation $modeCotisation): static
    {
        $this->modeCotisation = $modeCotisation;

        return $this;
    }
}