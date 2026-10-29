<?php

namespace App\Service;

use App\Dto\CampagneDetailDto;
use App\Dto\MembreCampagneDto;
use App\Dto\SeanceResumeDto;
use App\Repository\AmendeRepository;
use App\Repository\CampagneRepository;
use App\Repository\CotisationRepository;
use App\Repository\ModeCotisationRepository;
use App\Repository\SeanceRepository;

class CampagneService
{
    public function __construct(
        private readonly CampagneRepository $campagneRepository,
        private readonly ModeCotisationRepository $modeCotisationRepository,
        private readonly SeanceRepository $seanceRepository,
        private readonly CotisationRepository $cotisationRepository,
        private readonly AmendeRepository $amendeRepository,
    ) {
    }

    /**
     * Le détail d'une campagne avec ses membres et ses séances (null si elle n'existe pas).
     */
    public function detail(int $id): ?CampagneDetailDto
    {
        $campagne = $this->campagneRepository->findDetail($id);
        if (null === $campagne) {
            return null;
        }

        $membres = [];
        foreach ($this->modeCotisationRepository->findByCampagne($campagne) as $mode) {
            $membres[] = new MembreCampagneDto(
                $mode->getMembre()->getNomComplet(),
                $mode->isLibre() ? 'Montant libre' : 'Montant fixe',
            );
        }

        $totauxCotisations = $this->cotisationRepository->totauxParSeance($campagne);
        $totauxAmendes = $this->amendeRepository->totauxParSeance($campagne);

        $seances = [];
        foreach ($this->seanceRepository->findByCampagne($campagne) as $seance) {
            $seances[] = new SeanceResumeDto(
                $seance->getId(),
                $seance->getDateAt()->format('d/m/Y'),
                $totauxCotisations[$seance->getId()]['collecte'] ?? 0,
                $totauxCotisations[$seance->getId()]['caisse'] ?? 0,
                $totauxAmendes[$seance->getId()] ?? 0,
            );
        }

        $groupement = $campagne->getExercice()->getGroupement();

        return new CampagneDetailDto(
            $campagne->getId(),
            $campagne->getNom(),
            $campagne->getTypeCampagne()->getNom(),
            $campagne->getPeriodicite()?->label(),
            $campagne->getJourRencontre(),
            $campagne->getMontant(),
            $campagne->getCaissePresent(),
            $campagne->getCaisseAbsent(),
            $campagne->getAmendeJournaliere(),
            $campagne->getDateDebutAt()->format('d/m/Y'),
            $campagne->getDateFinAt()?->format('d/m/Y'),
            $groupement->getId(),
            $groupement->getNom(),
            $membres,
            $seances,
        );
    }
}