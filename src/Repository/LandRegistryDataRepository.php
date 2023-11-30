<?php

namespace App\Repository;

use App\Entity\LandRegistryData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LandRegistryData>
 *
 * @method LandRegistryData|null find($id, $lockMode = null, $lockVersion = null)
 * @method LandRegistryData|null findOneBy(array $criteria, array $orderBy = null)
 * @method LandRegistryData[]    findAll()
 * @method LandRegistryData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LandRegistryDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LandRegistryData::class);
    }

//    /**
//     * @return LandRegistryData[] Returns an array of LandRegistryData objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LandRegistryData
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
