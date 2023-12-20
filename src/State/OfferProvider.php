<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\State\SerializerAwareProviderTrait;
use App\Entity\Offer;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Project;
use App\Entity\Property;
use App\Pagination\OfferPaginator;
use App\Repository\ProjectRepository;
use App\Repository\PropertyRepository;

class OfferProvider implements ProviderInterface
{
    use SerializerAwareProviderTrait;

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): iterable {
        /** @var ProjectRepository $projectRepo */
        $projectRepo = $this->entityManager->getRepository(Project::class);
        /** @var PropertyRepository $propertyRepo */
        $propertyRepo = $this->entityManager->getRepository(Property::class);

        $projects = $projectRepo->findByFilters($context['filters']);
        $properties = $propertyRepo->findByFilters($context['filters']);

        $projects = array_map(
            fn(Project $item) => new Offer(
                'project',
                $item->getTitle(),
                $item->getImage(),
                null,
                $item->getStatus(),
                '',
                $item->getSlug(),
                null,
                $item->getNumberOfObjects(),
                null,
                $item->getBuildYear(),
                null,
            ), $projects);

        $properties = array_map(
            fn(Property $item) => new Offer(
                'property',
                $item->getTitle(),
                $item->getImage()->getUrl(),
                $item->getCondition(),
                $item->getStatus(),
                $item->getAddress(),
                $item->getSlug(),
                $item->getBedrooms(),
                null,
                null,
                null,
                $item->getPrice(),
            ), $properties);

        $combined = array_merge($projects, $properties);

        $totalItems = count($combined);

        // Example pagination values, adjust as needed
        $currentPage = $context['filters']['page'] ?? 1;
        $itemsPerPage = 10; // Define your items per page

        $items = array_slice($combined, ($currentPage - 1) * $itemsPerPage, $itemsPerPage);

        return new OfferPaginator($items, $totalItems, $currentPage, $itemsPerPage);
    }
}
