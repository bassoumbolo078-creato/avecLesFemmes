<?php

namespace App\Dto;

/**
 * Détail d'une campagne : sa configuration, ses membres et ses séances.
 */
final readonly class CampagneDetailDto
{
    /**
     * @param MembreCampagneDto[] $membres
     * @param SeanceResumeDto[]   $seances
     */
    public function __construct(
        public int $id,
        public string $nom,
        public string $type,
        public ?string $periodicite,
        public ?string $jourRencontre,
        public int $montant,
        public int $caissePresent,
        public int $caisseAbsent,
        public int $amendeJournaliere,
        public string $dateDebut,
        public ?string $dateFin,
        public int $groupementId,
        public string $groupementNom,
        public array $membres,
        public array $seances,
    ) {
    }
}