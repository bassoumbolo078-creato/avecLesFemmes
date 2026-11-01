<?php

namespace App\Dto;

/**
 * Une membre affectée à une campagne, avec son mode de cotisation.
 */
final readonly class MembreCampagneDto
{
    public function __construct(
        public string $nomComplet,
        public string $mode,
    ) {
    }
}