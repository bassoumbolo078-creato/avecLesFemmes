<?php

namespace App\DataFixtures;

use App\Entity\Seance;
use App\Enum\Periodicite;
use App\Repository\CampagneRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SeanceFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly CampagneRepository $campagneRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->campagneRepository->findBy([], ['id' => 'ASC']) as $campagne) {
            // [première date, intervalle, nombre de séances]
            [$date, $intervalle, $nombre] = match ($campagne->getPeriodicite()) {
                Periodicite::HEBDOMADAIRE => [new \DateTime('2026-01-04'), '+7 days', 4],
                default => [new \DateTime('2026-03-15'), '+1 month', 3],
            };

            for ($i = 0; $i < $nombre; ++$i) {
                $manager->persist(
                    (new Seance())
                        ->setDateAt(clone $date)
                        ->setCampagne($campagne)
                );
                $date->modify($intervalle);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [CampagneFixtures::class];
    }
}