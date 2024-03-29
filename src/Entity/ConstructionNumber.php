<?php

namespace App\Entity;

use ApiPlatform\Metadata\GraphQl\QueryCollection;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\ConstructionNumberRepository;
use App\State\ConstructionNumberProvider;
use Cocur\Slugify\Slugify;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use ReflectionClass;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity(repositoryClass: ConstructionNumberRepository::class)]
#[ApiResource(
    operations: [
        new Get(name: "getConstructionNumberItem"),
        new Get(name: "getExternalConstructionNumberItem", uriTemplate: '/constructionNumberExternal/{id}', provider: ConstructionNumberProvider::class),
        new GetCollection(name: "getConstructionNumberCollection")
    ],
    graphQlOperations: [
        new Query(name: 'item_query'),
        new QueryCollection(name: 'collection_query', paginationType: 'page')
    ]
)]
class ConstructionNumber
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups('read')]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(nullable: true)]
    private ?array $algemeen = null;

    #[ORM\Column]
    private ?int $externalId = null;

    #[ORM\Column(nullable: true)]
    private ?array $media = null;

    #[ORM\Column(nullable: true)]
    private ?array $address = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?array $teksten = null;

    #[ORM\Column(nullable: true)]
    private ?array $diversen = null;

    #[ORM\ManyToOne(inversedBy: 'constructionNumbers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Ignore]
    private ?ConstructionType $constructionType = null;

    #[Groups(['read', 'slug'])]
    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(nullable: true)]
    private ?array $detail = null;

    #[Groups('read')]
    #[ORM\Column(nullable: true)]
    private ?int $rooms = null;

    #[ORM\Column(nullable: true)]
    private ?int $bedrooms = null;

    #[ORM\Column(nullable: true)]
    private ?array $financieel = null;

    #[Groups('read')]
    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[Groups('read')]
    #[ORM\Column(nullable: true)]
    private ?int $livingArea = null;

    #[ORM\Embedded]
    #[Ignore]
    private Money $price;

    #[ORM\Column(length: 255)]
    private ?string $priceCondition = null;

    #[ORM\Column]
    #[Ignore]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 32, columnDefinition: 'CHAR(32) NOT NULL')]
    #[Ignore]
    private ?string $mediaHash = null;

    #[Groups('read')]
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $readableStatus = null;

    #[ORM\Column(length: 25, nullable: true)]
    private ?string $energyClass = null;

    public function map(ConstructionNumber $newProperties)
    {
        $reflectionClass = new ReflectionClass($this);
        foreach ($reflectionClass->getMethods() as $method) {
            if (str_starts_with($method->getName(), 'set')) {
                $propertyName = substr($method->getName(), 3);
                $setMethod = 'set' . $propertyName;
                $getMethod = 'get' . $propertyName;
                if ($propertyName !== 'ConstructionType' && $propertyName !== 'Media') {
                    $this->{$setMethod}($newProperties->{$getMethod}());
                }
                if ($propertyName === 'Media') {
                    if ($this->getUpdatedAt() < $newProperties->getUpdatedAt()) {
                        $this->{$setMethod}($newProperties->{$getMethod}());
                    }
                }
            }
        }
    }

    public function updateFromNewNumber(ConstructionNumber $newType)
    {
        $this->map($newType);
        $this->createSlug();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAlgemeen(): ?array
    {
        return $this->algemeen;
    }

    public function setAlgemeen(?array $algemeen): static
    {
        $this->algemeen = $algemeen;

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

    public function getMedia(): ?array
    {
        return $this->media;
    }

    public function setMedia(?array $media): static
    {
        $this->media = $media;

        return $this;
    }

    public function getTeksten(): ?array
    {
        return $this->teksten;
    }

    public function setTeksten(?array $teksten): static
    {
        $this->teksten = $teksten;

        return $this;
    }

    public function getDiversen(): ?array
    {
        return $this->diversen;
    }

    public function setDiversen(?array $diversen): static
    {
        $this->diversen = $diversen;

        return $this;
    }

    public function getConstructionType(): ?ConstructionType
    {
        return $this->constructionType;
    }

    public function setConstructionType(?ConstructionType $constructionType): static
    {
        $this->constructionType = $constructionType;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getAddress(): ?array
    {
        return $this->address;
    }

    public function setAddress(?array $address): static
    {
        $this->address = $address;

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
        $this->setSlug($this->getTitle() . '-' . $this->getExternalId());

        return $this;
    }

    public function getDetail(): ?array
    {
        return $this->detail;
    }

    public function setDetail(?array $detail): static
    {
        $this->detail = $detail;

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

    public function getFinancieel(): ?array
    {
        return $this->financieel;
    }

    public function setFinancieel(?array $financieel): static
    {
        $this->financieel = $financieel;

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

    public function getLivingArea(): ?int
    {
        return $this->livingArea;
    }

    public function setLivingArea(?int $livingArea): static
    {
        $this->livingArea = $livingArea;

        return $this;
    }

    public function getPrice(): Money
    {
        return $this->price;
    }

    #[Groups('read')]
    public function getFormattedPrice(): string
    {
        $currencies = new ISOCurrencies();

        $numberFormatter = new \NumberFormatter('nl_NL', \NumberFormatter::CURRENCY);
        $numberFormatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, 0);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);

        return "{$moneyFormatter->format($this->price)} {$this->priceCondition}";
    }

    public function setPrice(?Money $value): static
    {
        $this->price = $value ?? Money::EUR(0);

        return $this;
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

    public function getProjectSlug(): ?string
    {
        return $this->getConstructionType()?->getProject()?->getSlug();
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

    public function getMediaHash(): ?string
    {
        return $this->mediaHash;
    }

    public function setMediaHash(string $mediaHash): static
    {
        $this->mediaHash = $mediaHash;

        return $this;
    }

    public function getAmountOfSeperateToilets(): int
    {
        $bathrooms = 0;

        foreach ($this->getDetail()['etages'] as $etage) {
            if (is_array($etage['etagegegevens']['overigeRuimtes']) && in_array('TOILET', $etage['etagegegevens']['overigeRuimtes'])) {
                $bathrooms++;
            }
        }

        return $bathrooms;
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

    public function getEnergyClass(): ?string
    {
        return $this->energyClass;
    }

    public function setEnergyClass(?string $energyClass): static
    {
        $this->energyClass = $energyClass;

        return $this;
    }
}
