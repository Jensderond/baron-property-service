<?php

namespace App\Repository;

use App\Entity\PropertyDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PropertyDetail>
 *
 * @method PropertyDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method PropertyDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method PropertyDetail[]    findAll()
 * @method PropertyDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PropertyDetail::class);
    }

//    /**
//     * @return PropertyDetail[] Returns an array of PropertyDetail objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PropertyDetail
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
