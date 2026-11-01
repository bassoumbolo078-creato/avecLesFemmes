<?php

namespace App\Dto;

/**
 * Fiche d'un groupement : ses informations, son exercice en cours,
 * l'historique de ses exercices et toutes ses campagnes.
 */
final readonly class GroupementDetailDto
{
    /**
     * @param ExerciceHistoriqueDto[] $historique
     * @param CampagneDto[]           $campagnes
     */
    public function __construct(
        public int $id,
        public string $nom,
        public string $tel,
        public ?string $adresse,
        public int $nombreMembres,
        public ?ExerciceEnCoursDto $exerciceEnCours,
        public array $historique,
        public array $campagnes,
    ) {
    }
}