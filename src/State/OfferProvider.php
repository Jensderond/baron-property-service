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

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): iterable
    {
        /** @var ProjectRepository $projectRepo */
        $projectRepo = $this->entityManager->getRepository(Project::class);
        /** @var PropertyRepository $propertyRepo */
        $propertyRepo = $this->entityManager->getRepository(Property::class);

        $projects = $projectRepo->findByFilters($context['filters'] ?? []);
        $properties = $propertyRepo->findByFilters($context['filters'] ?? []);

        $projects = array_map(
            fn (Project $item) => new Offer(
                $item->getCreatedAt(),
                'project',
                $item->getTitle(),
                $item->getMainImage(),
                null,
                $item->getStatus(),
                '',
                $item->getSlug(),
                null,
                $item->getNumberOfObjects(),
                null,
                $item->getBuildYear(),
                null,
            ),
            $projects
        );

        $properties = array_map(
            fn (Property $item) => new Offer(
                $item->getCreatedAt(),
                'property',
                $item->getTitle(),
                $item->getImage(),
                $item->getCondition(),
                $item->getStatus(),
                $item->getAddress(),
                $item->getSlug(),
                $item->getBedrooms(),
                null,
                null,
                null,
                $item->getPrice(),
            ),
            $properties
        );

        $combined = array_merge($projects, $properties);

        // dump($combined);

        // sort $combined by date
        usort($combined, function ($a, $b) {
            // if($a->slug === 'villa-marehoek-0ong-oud-vossemeer-7505443') {
            //     dump($a->title, $b->title);
            //     dump($a->status === 'BESCHIKBAAR' && $b->status !== 'BESCHIKBAAR');
            // }
            if ($a->status === 'BESCHIKBAAR' && $b->status !== 'BESCHIKBAAR') {
                return -1;
            } elseif ($b->status === 'BESCHIKBAAR' && $a->status !== 'BESCHIKBAAR') {
                return 1;
            }

            // Additional sorting logic for other statuses if needed
            // For example, if you want to sort 'PROSPECT' and 'IN_AANMELDING' after 'BESCHIKBAAR'
            $order = ['PROSPECT', 'IN_AANMELDING'];
            foreach ($order as $status) {
                if ($a->status === $status && $b->status !== $status) {
                    return -1;
                } elseif ($b->status === $status && $a->status !== $status) {
                    return 1;
                }
            }

            if ($a->status === $b->status) {
                if ($a->itemType === 'property' && $b->itemType !== 'property') {
                    return -1;
                } elseif ($b->itemType === 'property' && $a->itemType !== 'property') {
                    return 1;
                }
            }

            return $a->createdAt <=> $b->createdAt;
        });

        $totalItems = count($combined);

        // Example pagination values, adjust as needed
        $currentPage = $context['filters']['page'] ?? 1;
        $itemsPerPage = 9; // Define your items per page

        $items = array_slice($combined, ($currentPage - 1) * $itemsPerPage, $itemsPerPage);

        return new OfferPaginator($items, $totalItems, $currentPage, $itemsPerPage);
    }
}
