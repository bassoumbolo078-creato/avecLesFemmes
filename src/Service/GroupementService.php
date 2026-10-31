<?php

namespace App\Service;

use App\Dto\CampagneDto;
use App\Dto\EtapeDto;
use App\Dto\ExerciceEnCoursDto;
use App\Dto\ExerciceHistoriqueDto;
use App\Dto\GroupementDetailDto;
use App\Dto\GroupementDto;
use App\Entity\Exercice;
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
                $groupement->getExercices()->count(),
                $exercice?->getDebutAt()?->format('d/m/Y'),
                $exercice?->getFinAt()?->format('d/m/Y'),
                $exercice?->getStatut()?->label(),
            );
        }

        return $dtos;
    }

    /**
     * La fiche d'un groupement (null s'il n'existe pas) : exercice en cours,
     * historique des exercices et toutes les campagnes.
     */
    public function detail(int $id): ?GroupementDetailDto
    {
        $groupement = $this->groupementRepository->find($id);
        if (null === $groupement) {
            return null;
        }

        // Tous les exercices, du plus récent au plus ancien
        $exercices = $this->exerciceRepository->findBy(['groupement' => $groupement], ['debutAt' => 'DESC']);

        $exerciceEnCours = null;
        $historique = [];
        foreach ($exercices as $exercice) {
            $enCours = StatutExercice::ENCOURS === $exercice->getStatut();

            if ($enCours && null === $exerciceEnCours) {
                $exerciceEnCours = new ExerciceEnCoursDto(
                    $exercice->getDebutAt()->format('d/m/Y'),
                    $exercice->getFinAt()?->format('d/m/Y'),
                    $this->etapes($exercice),
                );
            }

            $retribution = $exercice->getRetributionAt()?->format('d/m/Y');
            $historique[] = new ExerciceHistoriqueDto(
                $exercice->getDebutAt()->format('d/m/Y').' – '.($exercice->getFinAt()?->format('d/m/Y') ?? '…'),
                $exercice->getStatut()->label(),
                $exercice->getStatut()->value,
                null === $retribution ? '—' : ($enCours ? 'Prévue le '.$retribution : $retribution),
            );
        }

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
            $exerciceEnCours,
            $historique,
            $campagnes,
        );
    }

    /**
     * Les quatre dates clés d'un exercice, avec leur état par rapport à aujourd'hui :
     * passée, prochaine (la date future la plus proche) ou à venir.
     *
     * @return EtapeDto[]
     */
    private function etapes(Exercice $exercice): array
    {
        $aujourdhui = new \DateTimeImmutable('today');

        $dates = [
            'Début' => $exercice->getDebutAt(),
            'Date limite des prêts' => $exercice->getLimitePretsAt(),
            'Fin' => $exercice->getFinAt(),
            'Rétribution' => $exercice->getRetributionAt(),
        ];

        $prochaine = null;
        foreach ($dates as $nom => $date) {
            if (null !== $date && $date >= $aujourdhui && (null === $prochaine || $date < $dates[$prochaine])) {
                $prochaine = $nom;
            }
        }

        $etapes = [];
        foreach ($dates as $nom => $date) {
            if (null === $date) {
                continue;
            }

            $etat = $nom === $prochaine ? 'prochaine' : ($date < $aujourdhui ? 'passee' : '');

            $note = null;
            if ('Date limite des prêts' === $nom && 'prochaine' === $etat) {
                $jours = $aujourdhui->diff($date)->days;
                $note = sprintf('Encore %d jour%s pour prêter', $jours, $jours > 1 ? 's' : '');
            }

            $etapes[] = new EtapeDto($nom, $date->format('d/m/Y'), $etat, $note);
        }

        return $etapes;
    }
}