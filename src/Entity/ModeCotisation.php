<?php

namespace App\Entity;

use App\Repository\ModeCotisationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Table d'association entre une campagne et un membre (participation).
 * isLibre = false : le membre cotise le montant fixe de la campagne.
 * isLibre = true  : le membre cotise librement, le montant est saisi à chaque séance.
 */
#[ORM\Entity(repositoryClass: ModeCotisationRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_campagne_user', columns: ['campagne_id', 'user_id'])]
#[UniqueEntity(fields: ['campagne', 'user'], message: 'Ce membre participe déjà à cette campagne.')]
class ModeCotisation
{
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
    private ?User $user = null;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Dans une épargne, tout le monde cotise le montant fixe : isLibre doit rester false.
     */
    #[Assert\Callback]
    public function validateLibre(ExecutionContextInterface $context): void
    {
        if ($this->isLibre && $this->campagne instanceof Epargne) {
            $context->buildViolation('Une épargne a un montant fixe : le mode libre est réservé aux tontines.')
                ->atPath('isLibre')
                ->addViolation();
        }
    }
}