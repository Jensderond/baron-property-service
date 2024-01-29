<?php

namespace App\Repository;

use App\Entity\BogObject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BogObject>
 *
 * @method BogObject|null find($id, $lockMode = null, $lockVersion = null)
 * @method BogObject|null findOneBy(array $criteria, array $orderBy = null)
 * @method BogObject[]    findAll()
 * @method BogObject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BogObjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BogObject::class);
    }

    public function archiveMissing(array $idsInImport): int
    {
        $qb = $this->createQueryBuilder('p');

        if (empty($idsInImport)) {
            $count = $qb->select('count(p.externalId)')
                ->getQuery()
                ->getSingleScalarResult();

            $qb->delete()
                ->getQuery()
                ->execute();

            return $count;
        }

        $count = $qb->select('count(p.externalId)')
            ->where($qb->expr()->notIn('p.externalId', $idsInImport))
            ->getQuery()
            ->getSingleScalarResult();

        $qb->delete()
            ->where($qb->expr()->notIn('p.externalId', $idsInImport))
            ->getQuery()
            ->execute();

        return $count;
    }

//    /**
//     * @return BogObject[] Returns an array of BogObject objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BogObject
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
