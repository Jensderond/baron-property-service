<?php

namespace App\Controller;

use App\Entity\BogObject;
use App\Entity\ConstructionNumber;
use App\Entity\Project;
use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

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


        $allBogs = $em->getRepository(BogObject::class)->createQueryBuilder('p')
            ->where('p.archived = 0 OR p.archived is null')
            ->getQuery()
            ->getResult();

        $allProjects = $em->getRepository(Project::class)->createQueryBuilder('p')
            ->where('p.archived = 0 OR p.archived is null')
            ->getQuery()
            ->getResult();

        $allProjectsNumbers = $em->getRepository(ConstructionNumber::class)->createQueryBuilder('p')
            ->getQuery()
            ->getResult();

        $properties = [];

        foreach ($allProps as $prop) {
            $properties[] = [
                'id' => $prop->getExternalId(),
                'title' => $prop->getAddress(),
            ];
        }

        foreach ($allBogs as $prop) {
            $properties[] = [
                'id' => $prop->getExternalId(),
                'title' => $prop->getTitle(),
            ];
        }

        foreach ($allProjects as $prop) {
            $properties[] = [
                'id' => $prop->getExternalId(),
                'title' => $prop->getTitle(),
            ];
        }

        foreach ($allProjectsNumbers as $prop) {
            $properties[] = [
                'id' => $prop->getExternalId(),
                'title' => $prop->getConstructionType()->getProject()->getTitle() . ' - ' . $prop->getTitle(),
            ];
        }

        // sort all properties by title
        usort($properties, function ($a, $b) {
            return strcasecmp($a['title'], $b['title']);
        });

        return new JsonResponse(['properties' => $properties]);
    }
}
