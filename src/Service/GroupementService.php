<?php

namespace App\Service;

use App\Dto\GroupementDto;
use App\Repository\ExerciceRepository;
use App\Repository\GroupementRepository;

class GroupementService
{
    public function __construct(
        private readonly GroupementRepository $groupementRepository,
        private readonly ExerciceRepository $exerciceRepository,
    ) {
    }

    /**
     * La liste des groupements, chacun avec son exercice en cours.
     *
     * @return GroupementDto[]
     */
    public function lister(): array
    {
        // Exercice en cours de chaque groupement, indexé par l'id du groupement
        $enCours = [];
        foreach ($this->exerciceRepository->findEnCours() as $exercice) {
            $enCours[$exercice->getGroupement()->getId()] = $exercice;
        }

        $dtos = [];
        foreach ($this->groupementRepository->findBy([], ['nom' => 'ASC']) as $groupement) {
            $exercice = $enCours[$groupement->getId()] ?? null;

            $dtos[] = new GroupementDto(
                $groupement->getId(),
                $groupement->getNom(),
                $groupement->getTel(),
                $groupement->getAdresse(),
                $groupement->getMembres()->count(),
                $exercice?->getDebutAt()?->format('d/m/Y'),
                $exercice?->getFinAt()?->format('d/m/Y'),
                $exercice?->getStatut()?->label(),
            );
        }

        return $dtos;
    }
}