<?php

namespace App\State;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\State\SerializerAwareProviderTrait;
use App\Entity\BogObject;
use App\Entity\Offer;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Project;
use App\Entity\Property;
use App\Pagination\OfferPaginator;

class OfferProvider implements ProviderInterface
{
    use SerializerAwareProviderTrait;

    public function __construct(private ProviderInterface $collectionProvider, private EntityManagerInterface $entityManager)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): iterable
    {
        $bogObjectOperation = new GetCollection(name: "getBogObjectCollection", class: BogObject::class, filters: ['annotated_app_entity_bog_object_api_platform_doctrine_orm_filter_search_filter', 'annotated_app_entity_bog_object_api_platform_doctrine_orm_filter_boolean_filter'], paginationEnabled: false);
        $projectOperation = new GetCollection(name: "getProjectCollection", class: Project::class, filters: ['annotated_app_entity_project_api_platform_doctrine_orm_filter_search_filter', 'annotated_app_entity_project_api_platform_doctrine_orm_filter_boolean_filter'], paginationEnabled: false);
        $propertyOperation = new GetCollection(name: "getPropertyCollection", class: Property::class, filters: ['annotated_app_entity_property_api_platform_doctrine_orm_filter_search_filter', 'annotated_app_entity_property_api_platform_doctrine_orm_filter_boolean_filter'], paginationEnabled: false);
        /** @var BogObject[] $bogObjects*/
        $bogObjects = $this->collectionProvider->provide($bogObjectOperation, $uriVariables, $context);

        /** @var Project[] $projects*/
        $projects = $this->collectionProvider->provide($projectOperation, $uriVariables, $context);

        /** @var Property[] $properties */
        $properties = $this->collectionProvider->provide($propertyOperation, $uriVariables, $context);

        $bogObjects = array_map(
            fn (BogObject $item) => new Offer(
                $item->getCreatedAt()->format('Y-m-d'),
                $item->getUpdatedAt()->format('Y-m-d'),
                'bogObject',
                $item->getTitle(),
                $item->getImage(),
                null,
                $item->getStatus(),
                $item->getReadableStatus(),
                '',
                $item->getSlug(),
                null,
                null,
                $item->getPlot(),
                $item->getBuildYear(),
                $item->getFormattedPrice(),
            ),
            $bogObjects
        );

        $projects = array_map(
            fn (Project $item) => new Offer(
                $item->getCreatedAt()->format('Y-m-d'),
                $item->getUpdatedAt()->format('Y-m-d'),
                'project',
                $item->getTitle(),
                $item->getMainImage(),
                null,
                $item->getStatus(),
                $item->getReadableStatus(),
                '',
                $item->getSlug(),
                null,
                $item->getNumberOfObjects(),
                $item->getPlot(),
                $item->getBuildYear(),
                $item->getPriceRange(),
            ),
            $projects
        );

        $properties = array_map(
            fn (Property $item) => new Offer(
                $item->getCreatedAt()->format('Y-m-d'),
                $item->getUpdatedAt()->format('Y-m-d'),
                'property',
                $item->getTitle(),
                $item->getImage(),
                $item->getCondition(),
                $item->getStatus(),
                $item->getReadableStatus(),
                $item->getAddress(),
                $item->getSlug(),
                $item->getBedrooms(),
                null,
                $item->getPlot(),
                $item->getBuildYear(),
                $item->getFormattedPrice(),
            ),
            $properties
        );

        $combined = array_merge($bogObjects, $projects, $properties);

        usort($combined, function ($a, $b) {
            if ($a->status === 'BESCHIKBAAR' && $b->status !== 'BESCHIKBAAR') {
                return -1;
            } elseif ($b->status === 'BESCHIKBAAR' && $a->status !== 'BESCHIKBAAR') {
                return 1;
            }

            $order = ['VERKOCHT_ONDER_VOORBEHOUD', 'PROSPECT', 'IN_AANMELDING'];
            foreach ($order as $status) {
                if ($a->status === $status && $b->status !== $status) {
                    return -1;
                } elseif ($b->status === $status && $a->status !== $status) {
                    return 1;
                }
            }

            if($a->status === 'BESCHIKBAAR' && $b->status === 'BESCHIKBAAR') {
                return $b->createdAt <=> $a->createdAt;
            } else {
                return $b->updatedAt <=> $a->updatedAt;
            }
        });

        $totalItems = count($combined);

        // Example pagination values, adjust as needed
        $currentPage = $context['filters']['page'] ?? 1;
        $itemsPerPage = 9; // Define your items per page

        $items = array_slice($combined, ($currentPage - 1) * $itemsPerPage, $itemsPerPage);

        return new OfferPaginator($items, $totalItems, $currentPage, $itemsPerPage);
    }
}
