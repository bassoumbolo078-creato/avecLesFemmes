<?php

namespace App\DataFixtures;

use App\Entity\Campagne;
use App\Enum\Periodicite;
use App\Enum\StatutExercice;
use App\Repository\ExerciceRepository;
use App\Repository\TypeCampagneRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CampagneFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly ExerciceRepository $exerciceRepository,
        private readonly TypeCampagneRepository $typeCampagneRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $epargne = $this->typeCampagneRepository->findOneBy(['nom' => 'Épargne']);
        $tontine = $this->typeCampagneRepository->findOneBy(['nom' => 'Tontine']);

        $exercicesEnCours = $this->exerciceRepository->findBy(
            ['statut' => StatutExercice::ENCOURS],
            ['id' => 'ASC']
        );

        foreach ($exercicesEnCours as $exercice) {
            // Épargne calebasse : dure tout l'exercice, chaque dimanche
            $manager->persist(
                (new Campagne())
                    ->setNom('Épargne calebasse')
                    ->setDateDebutAt(clone $exercice->getDebutAt())
                    ->setDateFinAt(clone $exercice->getFinAt())
                    ->setMontant(5000)
                    ->setPeriodicite(Periodicite::HEBDOMADAIRE)
                    ->setJourRencontre('Dimanche')
                    ->setCaissePresent(100)
                    ->setCaisseAbsent(300)
                    ->setAmendeJournaliere(350)
                    ->setTypeCampagne($epargne)
                    ->setExercice($exercice)
            );

            // Tontine mensuelle, le 15 de chaque mois
            $manager->persist(
                (new Campagne())
                    ->setNom('Tontine mensuelle')
                    ->setDateDebutAt(new \DateTime('2026-03-15'))
                    ->setMontant(10000)
                    ->setPeriodicite(Periodicite::MENSUEL)
                    ->setJourRencontre('15')
                    ->setTypeCampagne($tontine)
                    ->setExercice($exercice)
            );
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ExerciceFixtures::class, TypeCampagneFixtures::class];
    }
}