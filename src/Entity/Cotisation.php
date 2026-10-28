<?php

namespace App\Entity;

use App\Enum\Presence;
use App\Repository\CotisationRepository;
use App\Trait\DateTimeTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Ce qu'une membre verse lors d'une séance : son épargne et sa part de caisse sociale.
 */
#[ORM\Entity(repositoryClass: CotisationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Cotisation
{
    use DateTimeTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: Presence::class)]
    private ?Presence $presence = null;

    /** Montant d'épargne versé (en FCFA). */
    #[ORM\Column(options: ['default' => 0])]
    private int $montant = 0;

    /** Part de caisse sociale versée (en FCFA). */
    #[ORM\Column(options: ['default' => 0])]
    private int $montantCaisse = 0;

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

    public function getPresence(): ?Presence
    {
        return $this->presence;
    }

    public function setPresence(Presence $presence): static
    {
        $this->presence = $presence;

        return $this;
    }

    public function getMontant(): int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getMontantCaisse(): int
    {
        return $this->montantCaisse;
    }

    public function setMontantCaisse(int $montantCaisse): static
    {
        $this->montantCaisse = $montantCaisse;

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