<?php

namespace App\DataFixtures;

use App\Entity\Exercice;
use App\Enum\StatutExercice;
use App\Repository\GroupementRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ExerciceFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly GroupementRepository $groupementRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->groupementRepository->findBy([], ['id' => 'ASC']) as $groupement) {
            // Un exercice clôturé (2025)...
            $manager->persist(
                (new Exercice())
                    ->setDebutAt(new \DateTime('2025-01-01'))
                    ->setFinAt(new \DateTime('2025-12-31'))
                    ->setLimitePretsAt(new \DateTime('2025-10-10'))
                    ->setRetributionAt(new \DateTime('2025-11-01'))
                    ->setStatut(StatutExercice::CLOTURE)
                    ->setGroupement($groupement)
            );

            // ... et l'exercice en cours (2026)
            $manager->persist(
                (new Exercice())
                    ->setDebutAt(new \DateTime('2026-01-01'))
                    ->setFinAt(new \DateTime('2026-12-31'))
                    ->setLimitePretsAt(new \DateTime('2026-10-10'))
                    ->setRetributionAt(new \DateTime('2026-11-01'))
                    ->setStatut(StatutExercice::ENCOURS)
                    ->setGroupement($groupement)
            );
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [GroupementFixtures::class];
    }
}