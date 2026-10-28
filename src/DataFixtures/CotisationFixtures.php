<?php

namespace App\DataFixtures;

use App\Entity\Cotisation;
use App\Enum\Presence;
use App\Repository\ModeCotisationRepository;
use App\Repository\SeanceRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CotisationFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly SeanceRepository $seanceRepository,
        private readonly ModeCotisationRepository $modeCotisationRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $numeroParCampagne = [];

        foreach ($this->seanceRepository->findBy([], ['id' => 'ASC']) as $seance) {
            $campagne = $seance->getCampagne();
            $numero = $numeroParCampagne[$campagne->getId()] ?? 0;
            $numeroParCampagne[$campagne->getId()] = $numero + 1;

            $modes = $this->modeCotisationRepository->findBy(['campagne' => $campagne], ['id' => 'ASC']);

            foreach ($modes as $i => $mode) {
                // Montant : fixe, ou variable pour une membre en mode libre
                $montant = $mode->isLibre()
                    ? 3000 + 1000 * ($numero % 3)
                    : $campagne->getMontant();

                // Alternance de présences pour avoir des cas variés
                [$presence, $versement, $caisse] = match (($i + $numero) % 5) {
                    0 => [Presence::ABSENT, 0, 0],
                    1 => [Presence::ABSENT_REPRESENTE, $montant, $campagne->getCaisseAbsent()],
                    default => [Presence::PRESENT, $montant, $campagne->getCaissePresent()],
                };

                $manager->persist(
                    (new Cotisation())
                        ->setSeance($seance)
                        ->setModeCotisation($mode)
                        ->setPresence($presence)
                        ->setMontant($versement)
                        ->setMontantCaisse($caisse)
                );
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [SeanceFixtures::class, ModeCotisationFixtures::class];
    }
}