<?php

namespace App\DataFixtures;

use App\Entity\Membre;
use App\Repository\GroupementRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MembreFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly GroupementRepository $groupementRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // Cinq membres par groupement, dans l'ordre des groupements
        $noms = [
            'Aminata Sow', 'Awa Diop', 'Fatou Ndiaye', 'Mariama Ba', 'Khady Fall',
            'Rokhaya Mbaye', 'Coumba Thiam', 'Ndèye Faye', 'Aïssatou Sy', 'Dieynaba Kane',
            'Seynabou Gueye', 'Mame Diarra Seck', 'Astou Diouf', 'Bineta Cissé', 'Ramatoulaye Ndao',
        ];
        $parGroupement = array_chunk($noms, 5);

        foreach ($this->groupementRepository->findBy([], ['id' => 'ASC']) as $i => $groupement) {
            foreach ($parGroupement[$i] as $nomComplet) {
                $manager->persist(
                    (new Membre())
                        ->setNomComplet($nomComplet)
                        ->setGroupement($groupement)
                );
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [GroupementFixtures::class];
    }
}