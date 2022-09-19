<?php

namespace App\Controller;

use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class SanityController extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    #[Route('/sanity/list-properties', name: 'sanity-list-properties')]
    public function overviewFilters(): JsonResponse
    {
        $em = $this->getEntityManager();

        $allProps = $em->getRepository(Property::class)->createQueryBuilder('p')
            ->where('p.archived = 0 OR p.archived is null')
            ->getQuery()
            ->getResult();

        $properties = [];

        foreach ($allProps as $prop) {
            $properties[] = [
                'id' => $prop->getId(),
                'title' => $prop->getAddress(),
            ];
        }

        return new JsonResponse(['properties' => $properties]);
    }
}
