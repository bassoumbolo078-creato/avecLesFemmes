<?php

namespace App\Dto;

/**
 * Une séance d'une campagne, avec ses trois totaux.
 */
final readonly class SeanceResumeDto
{
    public function __construct(
        public int $id,
        public string $date,
        public int $totalCollecte,
        public int $totalCaisse,
        public int $totalAmendes,
    ) {
    }
}