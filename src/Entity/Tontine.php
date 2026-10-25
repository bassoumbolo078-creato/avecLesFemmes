<?php

namespace App\Entity;

use App\Repository\TontineRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TontineRepository::class)]
class Tontine extends Campagne
{
    #[ORM\Column]
    private ?int $nombreDeTours = null;

    public function getNombreDeTours(): ?int
    {
        return $this->nombreDeTours;
    }

    public function setNombreDeTours(int $nombreDeTours): static
    {
        $this->nombreDeTours = $nombreDeTours;

        return $this;
    }
}