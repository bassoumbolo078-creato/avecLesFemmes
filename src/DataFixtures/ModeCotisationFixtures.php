<?php

namespace App\DataFixtures;

use App\Entity\ModeCotisation;
use App\Repository\CampagneRepository;
use App\Repository\MembreRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ModeCotisationFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly CampagneRepository $campagneRepository,
        private readonly MembreRepository $membreRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->campagneRepository->findBy([], ['id' => 'ASC']) as $campagne) {
            $membres = $this->membreRepository->findBy(
                ['groupement' => $campagne->getExercice()->getGroupement()],
                ['id' => 'ASC']
            );

            foreach ($membres as $i => $membre) {
                $manager->persist(
                    (new ModeCotisation())
                        ->setCampagne($campagne)
                        ->setMembre($membre)
                        // la 3e membre de chaque groupement cotise librement
                        ->setIsLibre($i === 2)
                );
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [CampagneFixtures::class, MembreFixtures::class];
    }
}