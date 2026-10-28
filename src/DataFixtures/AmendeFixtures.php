<?php

namespace App\DataFixtures;

use App\Entity\Amende;
use App\Enum\Presence;
use App\Repository\CotisationRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AmendeFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly CotisationRepository $cotisationRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // Une amende pour chaque membre absente qui n'a pas payé
        $absentes = $this->cotisationRepository->findBy(['presence' => Presence::ABSENT], ['id' => 'ASC']);

        foreach ($absentes as $cotisation) {
            $jours = 3; // absente le dimanche, de retour le mercredi
            $montant = $jours * $cotisation->getSeance()->getCampagne()->getAmendeJournaliere();

            if (0 === $montant) {
                continue; // pas d'amende dans cette campagne
            }

            $manager->persist(
                (new Amende())
                    ->setSeance($cotisation->getSeance())
                    ->setModeCotisation($cotisation->getModeCotisation())
                    ->setNombreJours($jours)
                    ->setMontant($montant)
            );
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [CotisationFixtures::class];
    }
}