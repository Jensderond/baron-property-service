<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\PropertyRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use ReflectionClass;

/**
 * A property.
 */
#[ApiFilter(filterClass: DateFilter::class, properties: ['created', 'updated'])]
#[ApiFilter(filterClass: SearchFilter::class, properties: ['city' => 'exact', 'category' => 'exact', 'archived' => 'exact', 'status' => 'exact', 'address' => 'partial'])]
#[ApiFilter(filterClass: BooleanFilter::class, properties: ['archived'])]
#[ApiFilter(filterClass: OrderFilter::class, properties: ['created', 'status'], arguments: ['orderParameterName' => 'order'])]
#[ApiResource(operations: [new Get(name: "getPropertyItem"), new GetCollection(name: "getPropertyCollection")], graphQlOperations: [new Query(name: 'item_query'), new QueryCollection(name: 'collection_query', paginationType: 'page')])]
#[Entity(repositoryClass: PropertyRepository::class)]
class Property
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer')]
    #[ApiProperty(identifier: true)]
    private $id;

    #[Column(length: 255)]
    private ?string $title = null;

    #[Column]
    private array $algemeen = [];

    #[Column]
    private array $financieel = [];

    #[Column]
    private array $teksten = [];

    #[Column(length: 255)]
    private ?string $status = null;

    #[Column(length: 255)]
    private ?string $category = null;

    #[OneToOne(cascade: ['persist', 'remove'])]
    private ?PropertyDetail $detail = null;

    #[Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[Column(nullable: true)]
    private ?int $houseNumber = null;

    #[Column(length: 255, nullable: true)]
    private ?string $houseNumberAddition = null;

    #[Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[Column(length: 255, nullable: true)]
    private ?string $zip = null;

    #[Column(length: 255, nullable: true)]
    private ?string $lat = null;

    #[Column(length: 255, nullable: true)]
    private ?string $lng = null;

    #[Column(length: 255)]
    private ?string $slug = null;

    #[Column]
    private ?int $externalId = null;

    #[Column(length: 255, nullable: true)]
    private ?string $street = null;

    #[Column(nullable: true)]
    private ?bool $archived = null;

    #[Column(nullable: true)]
    private ?int $build_year = null;

    #[Column(nullable: true)]
    private ?int $price = null;

    #[Column(nullable: true)]
    private ?int $rental_price = null;

    #[Column(length: 25, nullable: true)]
    private ?string $energyClass = null;

    #[OneToOne(cascade: ['persist', 'remove'])]
    private ?Media $image = null;

    #[OneToMany(mappedBy: 'property', targetEntity: Media::class, cascade: ['persist', 'remove'])]
    private Collection $media;

    public function __construct()
    {
        $this->media = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function map(Property $newProperties)
    {
        $reflectionClass = new ReflectionClass($this);
        foreach ($reflectionClass->getMethods() as $method) {
            if (str_starts_with($method->getName(), 'set')) {
                $setMethod = 'set' . substr($method->getName(), 3);
                $getMethod = 'get' . substr($method->getName(), 3);
                $this->{$setMethod}($newProperties->{$getMethod}());
            }
        }
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getAlgemeen(): array
    {
        return $this->algemeen;
    }

    public function setAlgemeen(array $algemeen): static
    {
        $this->algemeen = $algemeen;

        return $this;
    }

    public function getFinancieel(): array
    {
        return $this->financieel;
    }

    public function setFinancieel(array $financieel): static
    {
        $this->financieel = $financieel;

        return $this;
    }

    public function getTeksten(): array
    {
        return $this->teksten;
    }

    public function setTeksten(array $teksten): static
    {
        $this->teksten = $teksten;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getDetail(): ?PropertyDetail
    {
        return $this->detail;
    }

    public function setDetail(?PropertyDetail $detail): static
    {
        $this->detail = $detail;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getHouseNumber(): ?int
    {
        return $this->houseNumber;
    }

    public function setHouseNumber(?int $houseNumber): static
    {
        $this->houseNumber = $houseNumber;

        return $this;
    }

    public function getHouseNumberAddition(): ?string
    {
        return $this->houseNumberAddition;
    }

    public function setHouseNumberAddition(?string $houseNumberAddition): static
    {
        $this->houseNumberAddition = $houseNumberAddition;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function setZip(?string $zip): static
    {
        $this->zip = $zip;

        return $this;
    }

    public function getLat(): ?string
    {
        return $this->lat;
    }

    public function setLat(?string $lat): static
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?string
    {
        return $this->lng;
    }

    public function setLng(?string $lng): static
    {
        $this->lng = $lng;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        if (!isset($slug)) {
            $this->slug = '';
            return $this;
        }
        $slugify = new Slugify();
        $this->slug = $slugify->slugify($slug);

        return $this;
    }

    public function createSlug(): static
    {
        $this->setSlug($this->getStreet().'-'.$this->getHouseNumber().$this->getHouseNumberAddition().'-'.$this->getCity().'-'.$this->getExternalId());

        return $this;
    }

    public function getExternalId(): ?int
    {
        return $this->externalId;
    }

    public function setExternalId(int $externalId): static
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getArchived(): ?bool
    {
        return $this->archived;
    }

    public function setArchived(?bool $archived): static
    {
        $this->archived = $archived;

        return $this;
    }

    public function getBuildYear(): ?int
    {
        return $this->build_year;
    }

    public function setBuildYear(?int $build_year): static
    {
        $this->build_year = $build_year;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getRentalPrice(): ?int
    {
        return $this->rental_price;
    }

    public function setRentalPrice(?int $rental_price): static
    {
        $this->rental_price = $rental_price;

        return $this;
    }

    public function getEnergyClass(): ?string
    {
        return $this->energyClass;
    }

    public function setEnergyClass(?string $energyClass): static
    {
        $this->energyClass = $energyClass;

        return $this;
    }

    public function getImage(): ?Media
    {
        return $this->image;
    }

    public function setImage(?Media $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedium(Media $medium): static
    {
        if (!$this->media->contains($medium)) {
            $this->media->add($medium);
            $medium->setProperty($this);
        }

        return $this;
    }

    public function removeMedium(Media $medium): static
    {
        if ($this->media->removeElement($medium)) {
            // set the owning side to null (unless already changed)
            if ($medium->getProperty() === $this) {
                $medium->setProperty(null);
            }
        }

        return $this;
    }

    public function getCondition(): string
    {
        $condition = $this->getFinancieel()['overdracht']['koopconditie'] ?? $this->getFinancieel()['overdracht']['huurconditie'] ?? null;

        return match ($condition) {
            'KOSTEN_KOPER' => 'kk',
            'VRIJ_OP_NAAM' => 'von',
            'PER_MAAND' => 'per maand',
            'PER_JAAR' => 'per jaar',
            default => 'kk',
        };
    }

    public function getBedrooms(): ?int
    {
        // array reduce to loop over each
        $etages = $this->getDetail()->getEtages();

        $slaapkamers = array_reduce($etages, function ($carry, $item) {
            return $carry + $item['aantalSlaapkamers'];
        }, 0);

        return $slaapkamers;
    }
}
