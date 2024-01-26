<?php

namespace App\Controller;

use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class OverviewController extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    private function mergeAndRemoveDuplicatesCaseInsensitive($arrays, $key) {
        $caseInsensitiveMap = [];
        foreach ($arrays as $array) {
            foreach ($array as $subArray) {
                $value = $subArray[$key] ?? null;
                if ($value && !isset($caseInsensitiveMap[strtolower($value)])) {
                    $caseInsensitiveMap[strtolower($value)] = $value;
                }
            }
        }
        return array_values($caseInsensitiveMap);
    }

    #[Route('/properties/overview-filters', name: 'overview-filters')]
    public function overviewFilters(): JsonResponse
    {
        $em = $this->getEntityManager();

        // Query for Property
        $propertyCategories = $em->createQuery('SELECT DISTINCT p.category FROM App\Entity\Property p WHERE p.archived = 0 OR p.archived IS NULL')->getResult();
        $propertyCities = $em->createQuery('SELECT DISTINCT p.city FROM App\Entity\Property p WHERE p.archived = 0 OR p.archived IS NULL')->getResult();
        $propertyStatuses = $em->createQuery('SELECT DISTINCT p.status FROM App\Entity\Property p WHERE p.archived = 0 OR p.archived IS NULL')->getResult();

        // Query for BogObject
        $bogObjectCategories = $em->createQuery('SELECT DISTINCT b.category FROM App\Entity\BogObject b WHERE b.archived = 0 OR b.archived IS NULL')->getResult();
        $bogObjectCities = $em->createQuery('SELECT DISTINCT b.city FROM App\Entity\BogObject b WHERE b.archived = 0 OR b.archived IS NULL')->getResult();
        $bogObjectStatuses = $em->createQuery('SELECT DISTINCT b.status FROM App\Entity\BogObject b WHERE b.archived = 0 OR b.archived IS NULL')->getResult();

        // Query for Project
        $projectCategories = $em->createQuery('SELECT DISTINCT j.category FROM App\Entity\Project j WHERE j.archived = 0 OR j.archived IS NULL')->getResult();
        $projectCities = $em->createQuery('SELECT DISTINCT j.city FROM App\Entity\Project j WHERE j.archived = 0 OR j.archived IS NULL')->getResult();
        $projectStatuses = $em->createQuery('SELECT DISTINCT j.status FROM App\Entity\Project j WHERE j.archived = 0 OR j.archived IS NULL')->getResult();

        $categories = $this->mergeAndRemoveDuplicatesCaseInsensitive([$propertyCategories, $bogObjectCategories, $projectCategories], 'category');
        $cities = $this->mergeAndRemoveDuplicatesCaseInsensitive([$propertyCities, $bogObjectCities, $projectCities], 'city');
        $statuses = $this->mergeAndRemoveDuplicatesCaseInsensitive([$propertyStatuses, $bogObjectStatuses, $projectStatuses], 'status');

        return new JsonResponse(['filters' => ['categories' => $categories, 'cities' => $cities, 'statusses' => $statuses]]);
    }
}
