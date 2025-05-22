<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\BogObjectRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use ReflectionClass;
use App\State\BogObjectProvider;
use Symfony\Component\Serializer\Attribute\Ignore;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;

#[ApiFilter(filterClass: SearchFilter::class, properties: ['city' => 'exact', 'status' => 'exact', 'category' => 'exact', 'title' => 'partial'])]
#[ApiFilter(filterClass: BooleanFilter::class, properties: ['archived'])]
#[ApiResource(
    operations: [
        new Get(name: "getExternalBogObject", uriTemplate: '/bogObjectExternal/{id}', provider: BogObjectProvider::class),
        new GetCollection(name: "getBogObjectCollection")
    ]
)]

#[ORM\Entity(repositoryClass: BogObjectRepository::class)]
class BogObject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Ignore]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $externalId = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private array $diversen = [];

    #[ORM\Column(length: 255)]
    private string $category = '';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $zipCode = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $houseNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $street = null;

    #[ORM\Column(length: 255)]
    private ?string $mainFunction = null;

    #[ORM\Column(length: 30)]
    private ?string $status = null;

    #[ORM\Column(nullable: true)]
    private ?array $media = null;

    #[ORM\Column(nullable: true)]
    private ?array $image = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 32, columnDefinition: 'CHAR(32) NOT NULL')]
    #[Ignore]
    private ?string $mediaHash = null;

    #[ORM\Column]
    private array $finance = [];

    #[ORM\Column(nullable: true)]
    private ?int $price = null;

    #[ORM\Column(length: 25, nullable: true)]
    private ?string $energyClass = null;

    #[ORM\Column(nullable: true)]
    private ?int $buildYear = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column]
    #[Ignore]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Ignore]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $facilities = null;

    #[ORM\Column]
    private bool $archived = false;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $priceCondition = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $accessibility = null;

    #[ORM\Column(nullable: true)]
    private ?array $parking = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $localAmentities = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $plot = null;

    #[ORM\Column(nullable: true)]
    private ?array $functions = null;

    #[ORM\Column(nullable: true)]
    private ?int $numberOfFloors = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
    private ?float $lat = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
    private ?float $lng = null;

    #[ORM\Column(nullable: true)]
    private ?int $serviceCostPrice = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $serviceCostCondition = null;

    #[ORM\Column(nullable: true)]
    private ?bool $serviceCostVAT = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $readableStatus = null;

    #[ORM\Column(nullable: true)]
    private ?array $kadaster = null;

    public function map(BogObject $newProperties)
    {
        $reflectionClass = new ReflectionClass($this);

        foreach ($reflectionClass->getMethods() as $method) {
            if (str_starts_with($method->getName(), 'set')) {
                $propertyName = substr($method->getName(), 3);
                $setMethod = 'set' . $propertyName;
                $getMethod = 'get' . $propertyName;

                if (
                    $propertyName !== 'Media' &&
                    $propertyName !== 'Image' &&
                    $propertyName !== 'Lat' &&
                    $propertyName !== 'Lng'
                ) {
                    $this->{$setMethod}($newProperties->{$getMethod}());
                }
            }
        }
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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

    public function getDiversen(): array
    {
        return $this->diversen;
    }

    public function setDiversen(array $diversen): static
    {
        $this->diversen = $diversen;

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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(?string $zipCode): static
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getHouseNumber(): ?string
    {
        return $this->houseNumber;
    }

    public function setHouseNumber(?string $houseNumber): static
    {
        $this->houseNumber = $houseNumber;

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

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getMainFunction(): ?string
    {
        return $this->mainFunction;
    }

    public function setMainFunction(string $mainFunction): static
    {
        $this->mainFunction = $mainFunction;

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

    public function getMedia(): ?array
    {
        return $this->media;
    }

    public function setMedia(?array $media): static
    {
        $this->media = $media;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatetAt): static
    {
        $this->updatedAt = $updatetAt;

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

    public function getFinance(): array
    {
        return $this->finance;
    }

    public function setFinance(array $finance): static
    {
        $this->finance = $finance;

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

    public function getBuildYear(): ?int
    {
        return $this->buildYear;
    }

    public function setBuildYear(?int $buildYear): static
    {
        $this->buildYear = $buildYear;

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
        $this->setSlug($this->getStreet() . '-' . $this->getHouseNumber() . '-' . $this->getCity() . '-' . $this->getExternalId());

        return $this;
    }

    public function getFacilities(): ?string
    {
        return $this->facilities;
    }

    public function setFacilities(?string $facilities): static
    {
        $this->facilities = $facilities;

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

    public function getPriceCondition(): ?string
    {
        return $this->priceCondition;
    }

    public function setPriceCondition(?string $priceCondition): static
    {
        $this->priceCondition = $priceCondition;

        return $this;
    }

    public function getAccessibility(): ?string
    {
        return $this->accessibility;
    }

    public function setAccessibility(?string $accessibility): static
    {
        $this->accessibility = $accessibility;

        return $this;
    }

    public function getParking(): ?array
    {
        return $this->parking;
    }

    public function setParking(?array $parking): static
    {
        $this->parking = $parking;

        return $this;
    }

    public function getLocalAmentities(): ?string
    {
        return $this->localAmentities;
    }

    public function setLocalAmentities(?string $localAmentities): static
    {
        $this->localAmentities = $localAmentities;

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

    public function getFormattedPrice(): string
    {
        if ($this->price === 0) return '';

        $currencies = new ISOCurrencies();
        $numberFormatter = new \NumberFormatter('nl_NL', \NumberFormatter::CURRENCY);
        $numberFormatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, 0);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);

        // Check if property is both for sale and for rent
        if (isset($this->finance['overdracht']['koopEnOfHuur']['aanmeldingsreden']) && 
            $this->finance['overdracht']['koopEnOfHuur']['aanmeldingsreden'] === 'IN_VERKOOP_OF_VERHUUR_GENOMEN') {
            
            $salePrice = $this->price;
            $saleCondition = $this->priceCondition;
            
            // Get rental price and condition
            $rentalPrice = $this->finance['overdracht']['koopEnOfHuur']['huurprijs'] ?? 0;
            $rentalCondition = match($this->finance['overdracht']['koopEnOfHuur']['huurconditie'] ?? '') {
                'PER_JAAR' => 'p.j.',
                'PER_MAAND' => 'p.m.',
                'PER_VIERKANTE_METERS_PER_JAAR' => 'p.j. per m²',
                default => '',
            };
            
            // Format both prices
            $formattedSalePrice = $moneyFormatter->format(Money::EUR($salePrice * 100));
            $formattedRentalPrice = $rentalPrice > 0 
                ? $moneyFormatter->format(Money::EUR($rentalPrice * 100)) 
                : '';
            
            // Combine both prices if rental price exists
            if ($rentalPrice > 0) {
                return "{$formattedSalePrice} {$saleCondition} - {$formattedRentalPrice} {$rentalCondition}";
            }
        }
        
        // Default case: single price
        return "{$moneyFormatter->format(Money::EUR($this->price * 100))} {$this->priceCondition}";
    }

    public function getFunctions(): ?array
    {
        return $this->functions;
    }

    public function setFunctions(?array $functions): static
    {
        $this->functions = $functions;

        return $this;
    }

    public function getNumberOfFloors(): ?int
    {
        return $this->numberOfFloors;
    }

    public function setNumberOfFloors(?int $numberOfFloors): static
    {
        $this->numberOfFloors = $numberOfFloors;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat !== null ? (float) $this->lat : null;
    }

    public function setLat(?float $lat): self
    {
        $this->lat = $lat;
        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng !== null ? (float) $this->lng : null;
    }

    public function setLng(?float $lng): self
    {
        $this->lng = $lng;
        return $this;
    }

    public function getServiceCostPrice(): ?int
    {
        return $this->serviceCostPrice;
    }

    public function setServiceCostPrice(?int $serviceCostPrice): static
    {
        $this->serviceCostPrice = $serviceCostPrice;

        return $this;
    }

    public function getServiceCostCondition(): ?string
    {
        return $this->serviceCostCondition;
    }

    public function setServiceCostCondition(?string $serviceCostCondition): static
    {
        $this->serviceCostCondition = $serviceCostCondition;

        return $this;
    }

    public function getServiceCostVAT(): ?bool
    {
        return $this->serviceCostVAT;
    }

    public function setServiceCostVAT(?bool $serviceCostVAT): static
    {
        $this->serviceCostVAT = $serviceCostVAT;

        return $this;
    }

    public function getFormattedServicePrice(): ?string
    {
        if ($this->serviceCostPrice === 0 || $this->serviceCostPrice === null) return null;

        $currencies = new ISOCurrencies();

        $numberFormatter = new \NumberFormatter('nl_NL', \NumberFormatter::CURRENCY);
        $numberFormatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, 0);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);

        $vatApplied = $this->serviceCostVAT ? 'excl. btw' : 'incl. btw';

        return "{$moneyFormatter->format(Money::EUR($this->serviceCostPrice * 100))} {$this->serviceCostCondition} {$vatApplied}";
    }

    public function getReadableStatus(): ?string
    {
        return $this->readableStatus;
    }

    public function setReadableStatus(?string $readableStatus): static
    {
        $this->readableStatus = $readableStatus;

        return $this;
    }

    public function getKadaster(): ?array
    {
        return $this->kadaster;
    }

    public function setKadaster(?array $kadaster): static
    {
        $this->kadaster = $kadaster;

        return $this;
    }
}
