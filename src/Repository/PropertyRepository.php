<?php

namespace App\Repository;

use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Property>
 *
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
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
}
