<?php

namespace App\DataFixtures;

use App\Entity\Groupement;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GroupementFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $groupements = [
            ['Les Femmes de la Médina', '77 100 00 01', 'Médina, Dakar'],
            ['Union des Femmes de Thiaroye', '77 200 00 01', 'Thiaroye, Pikine'],
            ['Femmes Solidaires de Rufisque', '77 300 00 01', 'Rufisque'],
        ];

        foreach ($groupements as [$nom, $tel, $adresse]) {
            $groupement = (new Groupement())
                ->setNom($nom)
                ->setTel($tel)
                ->setAdresse($adresse);
            $manager->persist($groupement);
        }

        $manager->flush();
    }
}