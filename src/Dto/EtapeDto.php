<?php

namespace App\Dto;

/**
 * Une date clé d'un exercice, pour la frise (début, limite des prêts, fin, rétribution).
 * $etat vaut 'passee', 'prochaine' ou '' (à venir).
 */
final readonly class EtapeDto
{
    public function __construct(
        public string $nom,
        public string $date,
        public string $etat,
        public ?string $note,
    ) {
    }
}