<?php

namespace App\Controller;

use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

#[AsController]
class OverviewController extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    #[Route('/properties/overview-filters', name: 'overview-filters')]
    public function overviewFilters(): Response
    {
        $em = $this->getEntityManager();

        $types = $em->createQuery('SELECT DISTINCT p.type FROM App\Entity\Property p')->getResult();
        $cities = $em->createQuery('SELECT DISTINCT p.city FROM App\Entity\Property p')->getResult();
        $statusses = $em->createQuery('SELECT DISTINCT p.status FROM App\Entity\Property p')->getResult();

        $filteredTypes = [];
        foreach ($types as $type) {
            if($type['type'] !== null) {
                $filteredTypes[] = $type['type'];
            }
        }

        $filteredCities = [];
        foreach ($cities as $city) {
            if($city['city'] !== null) {
                $filteredCities[] = $city['city'];
            }
        }

        $filteredStatusses = [];
        foreach ($statusses as $status) {
            if($status['status'] !== null) {
                $filteredStatusses[] = $status['status'];
            }
        }

        return new JsonResponse(['filters' => ['types' => $filteredTypes, 'cities' => $filteredCities, 'statusses' => $filteredStatusses]]);
    }
}
