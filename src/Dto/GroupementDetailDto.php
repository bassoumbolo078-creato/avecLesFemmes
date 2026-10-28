<?php

namespace App\Dto;

/**
 * Détail d'un groupement : ses informations, son exercice en cours et toutes ses campagnes.
 */
final readonly class GroupementDetailDto
{
    /**
     * @param CampagneDto[] $campagnes
     */
    public function __construct(
        public int $id,
        public string $nom,
        public string $tel,
        public ?string $adresse,
        public int $nombreMembres,
        public ?string $exerciceDebut,
        public ?string $exerciceFin,
        public ?string $exerciceStatut,
        public array $campagnes,
    ) {
    }
}