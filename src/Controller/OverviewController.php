<?php

namespace App\Controller;

use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class OverviewController extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    #[Route('/properties/overview-filters', name: 'overview-filters')]
    public function overviewFilters(): JsonResponse
    {
        $em = $this->getEntityManager();

        $categories = $em->createQuery('SELECT DISTINCT p.category FROM App\Entity\Property p')->getResult();
        $cities = $em->createQuery('SELECT DISTINCT p.city FROM App\Entity\Property p')->getResult();
        $statusses = $em->createQuery('SELECT DISTINCT p.status FROM App\Entity\Property p')->getResult();
        $types = $em->createQuery('SELECT DISTINCT p.type FROM App\Entity\Property p')->getResult();

        $filteredCategories = [];
        foreach ($categories as $category) {
            if (null !== $category['category']) {
                $filteredCategories[] = $category['category'];
            }
        }

        $filteredCities = [];
        foreach ($cities as $city) {
            if (null !== $city['city']) {
                $filteredCities[] = $city['city'];
            }
        }

        $filteredStatusses = [];
        foreach ($statusses as $status) {
            if (null !== $status['status']) {
                $filteredStatusses[] = $status['status'];
            }
        }

        $filteredTypes = [];
        foreach ($types as $type) {
            if (null !== $type['type']) {
                $filteredTypes[] = $type['type'];
            }
        }

        return new JsonResponse(['filters' => ['categories' => $filteredCategories, 'types' => $filteredTypes, 'cities' => $filteredCities, 'statusses' => $filteredStatusses]]);
    }
}
