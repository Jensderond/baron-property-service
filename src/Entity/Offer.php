<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\State\OfferProvider;
use DateTimeImmutable;

#[ApiResource(
    operations: [
        new GetCollection(name: "getOfferCollection", provider: OfferProvider::class)
    ]
)]
#[ApiFilter(filterClass: SearchFilter::class, properties: ['city' => 'exact'])]
class Offer
{
    public function __construct(
        public readonly ?DateTimeImmutable $createdAt,
        public readonly ?string $itemType,
        public readonly ?string $title,
        public readonly ?array $image,
        public readonly ?string $condition,
        public readonly ?string $status,
        public readonly ?string $address,
        public readonly ?string $slug,
        public readonly ?int $bedrooms,
        public readonly ?int $numberOfObjects,
        public readonly ?int $plotSurface,
        public readonly ?string $buildYear,
        public readonly ?float $price,
    ) {
    }
}
