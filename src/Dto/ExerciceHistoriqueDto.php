<?php

namespace App\Dto;

/**
 * Une ligne de l'historique des exercices d'un groupement.
 * $statutCode vaut 'en_cours' ou 'cloture' (il sert de style CSS).
 */
final readonly class ExerciceHistoriqueDto
{
    public function __construct(
        public string $periode,
        public string $statut,
        public string $statutCode,
        public string $retribution,
    ) {
    }
}