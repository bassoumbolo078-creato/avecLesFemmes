<?php

namespace App\Repository;

use App\Entity\Campagne;
use App\Entity\Groupement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Campagne>
 */
class CampagneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Campagne::class);
    }

    /**
     * Toutes les campagnes d'un groupement (tous exercices confondus),
     * avec leur exercice et leur type, en une seule requête.
     *
     * @return Campagne[]
     */
    public function findByGroupement(Groupement $groupement): array
    {
        return $this->createQueryBuilder('c')
            ->addSelect('e', 't')
            ->join('c.exercice', 'e')
            ->join('c.typeCampagne', 't')
            ->andWhere('e.groupement = :groupement')
            ->setParameter('groupement', $groupement)
            ->orderBy('e.debutAt', 'DESC')
            ->addOrderBy('c.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Une campagne avec son exercice, son groupement et son type (une seule requête).
     */
    public function findDetail(int $id): ?Campagne
    {
        return $this->createQueryBuilder('c')
            ->addSelect('e', 'g', 't')
            ->join('c.exercice', 'e')
            ->join('e.groupement', 'g')
            ->join('c.typeCampagne', 't')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}