<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\State\OfferProvider;

#[ApiResource(
    operations: [
        new GetCollection(name: "getOfferCollection", provider: OfferProvider::class, filters: ['annotated_app_entity_offer_api_platform_doctrine_orm_filter_search_filter'])
    ]
)]
#[ApiFilter(filterClass: SearchFilter::class, properties: ['city' => 'exact', 'category' => 'exact', 'title' => 'partial'])]
#[ApiFilter(filterClass: BooleanFilter::class, properties: ['archived'])]
class Offer
{
    public function __construct(
        public readonly ?string $createdAt,
        public readonly ?string $itemType,
        public readonly ?string $title,
        public readonly ?array $image,
        public readonly ?string $condition,
        public readonly ?string $status,
        public readonly ?string $address,
        public readonly ?string $slug,
        public readonly ?string $bedrooms,
        public readonly ?int $numberOfObjects,
        public readonly ?int $plotSurface,
        public readonly ?string $buildYear,
        public readonly ?string $price,
    ) {
    }
}
