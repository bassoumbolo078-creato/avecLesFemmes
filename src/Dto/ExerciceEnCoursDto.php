<?php

namespace App\Dto;

/**
 * L'exercice en cours d'un groupement, avec sa frise de dates.
 */
final readonly class ExerciceEnCoursDto
{
    /**
     * @param EtapeDto[] $etapes
     */
    public function __construct(
        public string $debut,
        public ?string $fin,
        public array $etapes,
    ) {
    }
}