<?php

namespace App\Repository;

use App\Entity\ConstructionNumber;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConstructionNumber>
 *
 * @method ConstructionNumber|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConstructionNumber|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConstructionNumber[]    findAll()
 * @method ConstructionNumber[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConstructionNumberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConstructionNumber::class);
    }

    public function findAll()
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.archived = 0 OR p.archived is null')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return ConstructionNumber[] Returns an array of ConstructionNumber objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ConstructionNumber
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
