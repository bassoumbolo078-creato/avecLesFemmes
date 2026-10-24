<?php

namespace App\Entity;

use App\Repository\EpargneRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EpargneRepository::class)]
class Epargne extends Campagne
{
    #[ORM\Column(nullable: true)]
    private ?float $objectif = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $finalite = null;

    public function getObjectif(): ?float
    {
        return $this->objectif;
    }

    public function setObjectif(?float $objectif): static
    {
        $this->objectif = $objectif;

        return $this;
    }

    public function getFinalite(): ?string
    {
        return $this->finalite;
    }

    public function setFinalite(?string $finalite): static
    {
        $this->finalite = $finalite;

        return $this;
    }
}