<?php

namespace App\Entity;

use App\Enum\Periodicite;
use App\Repository\CampagneRepository;
use App\Trait\DateTimeTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CampagneRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Campagne
{
    use DateTimeTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateDebutAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $dateFinAt = null;

    /** Montant fixe de la cotisation par séance (en FCFA). */
    #[ORM\Column]
    private ?int $montant = null;

    #[ORM\Column(enumType: Periodicite::class, nullable: true)]
    private ?Periodicite $periodicite = null;

    /** Jour de rencontre (ex. : « dimanche », ou le jour du mois). */
    #[ORM\Column(length: 30, nullable: true)]
    private ?string $jourRencontre = null;

    /** Cotisation de caisse sociale d'un membre présent à la séance. */
    #[ORM\Column(options: ['default' => 0])]
    private int $caissePresent = 0;

    /** Cotisation de caisse sociale d'un membre absent mais représenté. */
    #[ORM\Column(options: ['default' => 0])]
    private int $caisseAbsent = 0;

    /** Amende par jour pour un membre absent qui n'a pas payé. */
    #[ORM\Column(options: ['default' => 0])]
    private int $amendeJournaliere = 0;

    #[ORM\ManyToOne(inversedBy: 'campagnes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeCampagne $typeCampagne = null;

    #[ORM\ManyToOne(inversedBy: 'campagnes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Exercice $exercice = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateDebutAt(): ?\DateTime
    {
        return $this->dateDebutAt;
    }

    public function setDateDebutAt(\DateTime $dateDebutAt): static
    {
        $this->dateDebutAt = $dateDebutAt;

        return $this;
    }

    public function getDateFinAt(): ?\DateTime
    {
        return $this->dateFinAt;
    }

    public function setDateFinAt(?\DateTime $dateFinAt): static
    {
        $this->dateFinAt = $dateFinAt;

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

    public function getPeriodicite(): ?Periodicite
    {
        return $this->periodicite;
    }

    public function setPeriodicite(?Periodicite $periodicite): static
    {
        $this->periodicite = $periodicite;

        return $this;
    }

    public function getJourRencontre(): ?string
    {
        return $this->jourRencontre;
    }

    public function setJourRencontre(?string $jourRencontre): static
    {
        $this->jourRencontre = $jourRencontre;

        return $this;
    }

    public function getCaissePresent(): int
    {
        return $this->caissePresent;
    }

    public function setCaissePresent(int $caissePresent): static
    {
        $this->caissePresent = $caissePresent;

        return $this;
    }

    public function getCaisseAbsent(): int
    {
        return $this->caisseAbsent;
    }

    public function setCaisseAbsent(int $caisseAbsent): static
    {
        $this->caisseAbsent = $caisseAbsent;

        return $this;
    }

    public function getAmendeJournaliere(): int
    {
        return $this->amendeJournaliere;
    }

    public function setAmendeJournaliere(int $amendeJournaliere): static
    {
        $this->amendeJournaliere = $amendeJournaliere;

        return $this;
    }

    public function getTypeCampagne(): ?TypeCampagne
    {
        return $this->typeCampagne;
    }

    public function setTypeCampagne(?TypeCampagne $typeCampagne): static
    {
        $this->typeCampagne = $typeCampagne;

        return $this;
    }

    public function getExercice(): ?Exercice
    {
        return $this->exercice;
    }

    public function setExercice(?Exercice $exercice): static
    {
        $this->exercice = $exercice;

        return $this;
    }
}