<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use App\Repository\PropertyRepository;
use App\State\PropertyProvider;
use Cocur\Slugify\Slugify;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use ReflectionClass;
use Symfony\Component\Serializer\Attribute\Ignore;

/**
 * A property.
 */
#[ApiFilter(filterClass: DateFilter::class, properties: ['created', 'updated'])]
#[ApiFilter(filterClass: SearchFilter::class, properties: ['city' => 'exact', 'category' => 'exact', 'archived' => 'exact', 'status' => 'exact', 'title' => 'partial'])]
#[ApiFilter(filterClass: BooleanFilter::class, properties: ['archived'])]
#[ApiFilter(filterClass: OrderFilter::class, properties: ['created', 'status'], arguments: ['orderParameterName' => 'order'])]
#[ApiResource(
    operations: [
        new Get(name: "getPropertyItem"),
        new GetCollection(name: "getPropertyCollection"),
        new Get(
            name: "getByExternalId",
            uriTemplate: "/properties/external/{id}",
            provider: PropertyProvider::class
        )
    ],
    graphQlOperations: [new Query(name: 'item_query'), new QueryCollection(name: 'collection_query', paginationType: 'page')]
)]

#[ORM\Entity(repositoryClass: PropertyRepository::class)]
class Property
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[ApiProperty(identifier: true)]
    private $id;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private array $algemeen = [];

    #[ORM\Column]
    private array $financieel = [];

    #[ORM\Column]
    private array $teksten = [];

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(length: 255)]
    private ?string $category = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(nullable: true)]
    private ?int $houseNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $houseNumberAddition = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $zip = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lat = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lng = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column]
    private ?int $externalId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $street = null;

    #[ORM\Column]
    private bool $archived = false;

    #[ORM\Column(nullable: true)]
    private ?int $build_year = null;

    #[ORM\Column(nullable: true)]
    private ?int $price = null;

    #[ORM\Column(length: 25, nullable: true)]
    private ?string $energyClass = null;

    #[ORM\Column(nullable: true)]
    private ?array $image = null;

    #[ORM\Column(nullable: true)]
    private ?array $media = null;

    #[ORM\Column]
    #[Ignore]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Ignore]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?array $etages = null;

    #[ORM\Column(nullable: true)]
    #[Ignore]
    private ?array $overigOnroerendGoed = null;

    #[ORM\Column(nullable: true)]
    private ?array $buitenruimte = null;

    #[ORM\Column(length: 255)]
    private ?string $priceCondition = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $plot = null;

    #[ORM\Column(length: 32, columnDefinition: 'CHAR(32) NOT NULL')]
    #[Ignore]
    private ?string $mediaHash = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $rooms = null;

    #[ORM\Column(nullable: true)]
    private ?int $bedrooms = null;

    #[ORM\Column(nullable: true)]
    private ?int $livingArea = null;

    public function __construct()
    {
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
                $propertyName = substr($method->getName(), 3);
                $setMethod = 'set' . $propertyName;
                $getMethod = 'get' . $propertyName;

                if ($propertyName !== 'Media' && $propertyName !== 'Image') {
                    $this->{$setMethod}($newProperties->{$getMethod}());
                }
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

    public function getEnergyClass(): ?string
    {
        return $this->energyClass;
    }

    public function setEnergyClass(?string $energyClass): static
    {
        $this->energyClass = $energyClass;

        return $this;
    }

    public function getImage(): ?array
    {
        return $this->image;
    }

    public function setImage(?array $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getMedia(): ?array
    {
        return $this->media;
    }

    public function setMedia(?array $media): static
    {
        $this->media = $media;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getEtages(): ?array
    {
        return $this->etages;
    }

    public function setEtages(?array $etages): static
    {
        $this->etages = $etages;

        return $this;
    }

    public function getOverigOnroerendGoed(): ?array
    {
        return $this->overigOnroerendGoed;
    }

    public function setOverigOnroerendGoed(?array $overigOnroerendGoed): static
    {
        $this->overigOnroerendGoed = $overigOnroerendGoed;

        return $this;
    }

    public function getBuitenruimte(): ?array
    {
        return $this->buitenruimte;
    }

    public function setBuitenruimte(?array $buitenruimte): static
    {
        $this->buitenruimte = $buitenruimte;

        return $this;
    }

    public function getFormattedPrice(): string
    {
        $currencies = new ISOCurrencies();

        $numberFormatter = new \NumberFormatter('nl_NL', \NumberFormatter::CURRENCY);
        $numberFormatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, 0);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);

        return "{$moneyFormatter->format(Money::EUR($this->price * 100))} {$this->priceCondition}";
    }

    public function getPriceCondition(): ?string
    {
        return $this->priceCondition;
    }

    public function setPriceCondition(string $priceCondition): static
    {
        $this->priceCondition = $priceCondition;

        return $this;
    }

    public function getPlot(): ?string
    {
        return $this->plot;
    }

    public function setPlot(?string $plot): static
    {
        $this->plot = $plot;

        return $this;
    }

    public function getMediaHash(): ?string
    {
        return $this->mediaHash;
    }

    public function setMediaHash(string $mediaHash): static
    {
        $this->mediaHash = $mediaHash;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(?int $rooms): static
    {
        $this->rooms = $rooms;

        return $this;
    }

    public function getBedrooms(): ?int
    {
        return $this->bedrooms;
    }

    public function setBedrooms(?int $bedrooms): static
    {
        $this->bedrooms = $bedrooms;

        return $this;
    }

    public function getLivingArea(): ?int
    {
        return $this->livingArea;
    }

    public function setLivingArea(?int $livingArea): static
    {
        $this->livingArea = $livingArea;

        return $this;
    }
}
