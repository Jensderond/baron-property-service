<?php

namespace App\Repository;

use App\Entity\ConstructionType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConstructionType>
 *
 * @method ConstructionType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConstructionType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConstructionType[]    findAll()
 * @method ConstructionType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConstructionTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConstructionType::class);
    }

    //    /**
    //     * @return ConstructionType[] Returns an array of ConstructionType objects
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

    //    public function findOneBySomeField($value): ?ConstructionType
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
