<?php

namespace App\Service;

use App\Dto\CampagneDto;
use App\Dto\GroupementDetailDto;
use App\Dto\GroupementDto;
use App\Enum\StatutExercice;
use App\Repository\CampagneRepository;
use App\Repository\ExerciceRepository;
use App\Repository\GroupementRepository;
use App\Repository\ModeCotisationRepository;

class GroupementService
{
    public function __construct(
        private readonly GroupementRepository $groupementRepository,
        private readonly ExerciceRepository $exerciceRepository,
        private readonly CampagneRepository $campagneRepository,
        private readonly ModeCotisationRepository $modeCotisationRepository,
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

    /**
     * Le détail d'un groupement avec toutes ses campagnes (null s'il n'existe pas).
     */
    public function detail(int $id): ?GroupementDetailDto
    {
        $groupement = $this->groupementRepository->find($id);
        if (null === $groupement) {
            return null;
        }

        $exercice = $this->exerciceRepository->findOneBy([
            'groupement' => $groupement,
            'statut' => StatutExercice::ENCOURS,
        ]);

        $nombreMembres = $this->modeCotisationRepository->compterParCampagne();

        $campagnes = [];
        foreach ($this->campagneRepository->findByGroupement($groupement) as $campagne) {
            $periode = $campagne->getExercice();

            $campagnes[] = new CampagneDto(
                $campagne->getId(),
                $campagne->getNom(),
                $campagne->getTypeCampagne()->getNom(),
                $campagne->getPeriodicite()?->label(),
                $campagne->getJourRencontre(),
                $campagne->getMontant(),
                $periode->getDebutAt()->format('d/m/Y').' → '.$periode->getFinAt()?->format('d/m/Y'),
                $campagne->getDateDebutAt()->format('d/m/Y'),
                $campagne->getDateFinAt()?->format('d/m/Y'),
                $nombreMembres[$campagne->getId()] ?? 0,
            );
        }

        return new GroupementDetailDto(
            $groupement->getId(),
            $groupement->getNom(),
            $groupement->getTel(),
            $groupement->getAdresse(),
            $groupement->getMembres()->count(),
            $exercice?->getDebutAt()?->format('d/m/Y'),
            $exercice?->getFinAt()?->format('d/m/Y'),
            $exercice?->getStatut()?->label(),
            $campagnes,
        );
    }
}