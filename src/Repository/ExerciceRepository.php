<?php

namespace App\Repository;

use App\Entity\Exercice;
use App\Enum\StatutExercice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Exercice>
 */
class ExerciceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exercice::class);
    }

    /**
     * Les exercices en cours de tous les groupements (avec leur groupement, en une seule requête).
     *
     * @return Exercice[]
     */
    public function findEnCours(): array
    {
        return $this->createQueryBuilder('e')
            ->addSelect('g')
            ->join('e.groupement', 'g')
            ->andWhere('e.statut = :statut')
            ->setParameter('statut', StatutExercice::ENCOURS)
            ->getQuery()
            ->getResult();
    }
}