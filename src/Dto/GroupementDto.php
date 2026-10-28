<?php

namespace App\Dto;

/**
 * Données d'un groupement prêtes à être affichées (dates déjà converties en texte).
 */
final readonly class GroupementDto
{
    public function __construct(
        public int $id,
        public string $nom,
        public string $tel,
        public ?string $adresse,
        public int $nombreMembres,
        public int $nombreExercices,
        public ?string $exerciceDebut,
        public ?string $exerciceFin,
        public ?string $exerciceStatut,
    ) {
    }
}