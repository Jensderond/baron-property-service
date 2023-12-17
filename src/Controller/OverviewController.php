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

        $propertyCategories = $em->createQuery('SELECT DISTINCT p.category FROM App\Entity\Property p WHERE p.archived = 0 OR p.archived is null')->getResult();
        $cities = $em->createQuery('SELECT DISTINCT p.city FROM App\Entity\Property p WHERE p.archived = 0 OR p.archived is null')->getResult();
        $statusses = $em->createQuery('SELECT DISTINCT p.status FROM App\Entity\Property p WHERE p.archived = 0 OR p.archived is null')->getResult();

        $filteredCategories = [];
        foreach ($propertyCategories as $category) {
            if (checkEmptyItem($category['category'])) {
                $filteredCategories[] = $category['category'];
            }
        }

        $filteredCities = [];
        foreach ($cities as $city) {
            if (checkEmptyItem($city['city'])) {
                $filteredCities[] = $city['city'];
            }
        }

        $filteredStatusses = [];
        foreach ($statusses as $status) {
            if (checkEmptyItem($status['status'])) {
                $filteredStatusses[] = $status['status'];
            }
        }

        return new JsonResponse(['filters' => ['categories' => $filteredCategories, 'cities' => $filteredCities, 'statusses' => $filteredStatusses]]);
    }
}


function checkEmptyItem(string | null $item): bool
{
    return null !== $item && $item !== "";
}
