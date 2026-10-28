<?php

namespace App\Dto;

/**
 * Données d'une campagne prêtes à être affichées.
 */
final readonly class CampagneDto
{
    public function __construct(
        public int $id,
        public string $nom,
        public string $type,
        public ?string $periodicite,
        public ?string $jourRencontre,
        public int $montant,
        public string $exercice,
        public string $dateDebut,
        public ?string $dateFin,
        public int $nombreMembres,
    ) {
    }
}