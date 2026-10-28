<?php

namespace App\Entity;

use App\Repository\ModeCotisationRepository;
use App\Trait\DateTimeTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Table d'association entre une campagne et une membre.
 * isLibre = false : la membre cotise le montant fixe de la campagne.
 * isLibre = true  : la membre cotise librement, le montant est saisi à chaque séance.
 */
#[ORM\Entity(repositoryClass: ModeCotisationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ModeCotisation
{
    use DateTimeTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private bool $isLibre = false;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campagne $campagne = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Membre $membre = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isLibre(): bool
    {
        return $this->isLibre;
    }

    public function setIsLibre(bool $isLibre): static
    {
        $this->isLibre = $isLibre;

        return $this;
    }

    public function getCampagne(): ?Campagne
    {
        return $this->campagne;
    }

    public function setCampagne(?Campagne $campagne): static
    {
        $this->campagne = $campagne;

        return $this;
    }

    public function getMembre(): ?Membre
    {
        return $this->membre;
    }

    public function setMembre(?Membre $membre): static
    {
        $this->membre = $membre;

        return $this;
    }
}