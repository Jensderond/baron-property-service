<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 *
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function findAll()
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.archived', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function archiveMissing(array $idsInImport): int
    {
        $qb = $this->createQueryBuilder('p');

        if (empty($idsInImport)) {
            $count = $qb->select('count(p.externalId)')
                ->getQuery()
                ->getSingleScalarResult();

            $qb->update()
                ->set('p.archived', true)
                ->getQuery()
                ->execute();

            return $count;
        }

        $count = $qb->select('count(p.externalId)')
            ->where($qb->expr()->notIn('p.externalId', $idsInImport))
            ->andWhere('p.archived = 0')
            ->getQuery()
            ->getSingleScalarResult();

        $qb->update()
            ->set('p.archived', true)
            ->where($qb->expr()->notIn('p.externalId', $idsInImport))
            ->andWhere('p.archived = 0')
            ->getQuery()
            ->execute();

        return $count;
    }

    //    /**
    //     * @return Project[] Returns an array of Project objects
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

    //    public function findOneBySomeField($value): ?Project
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
