<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use App\Repository\ProjectRepository;
use App\State\ProjectProvider;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use ReflectionClass;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ApiResource(
    operations: [
        new Get(name: "getProjectItem"),
        new Get(name: "getExternalProjectItem", uriTemplate: '/projectExternal/{id}', provider: ProjectProvider::class, normalizationContext: ['groups' => ['read']]),
        new GetCollection(name: "getProjectCollection", provider: ProjectProvider::class, normalizationContext: ['groups' => ['slug']])
    ],
    graphQlOperations: [
        new Query(name: 'item_query'),
        new QueryCollection(name: 'collection_query', paginationType: 'page')
    ]
)]
#[ApiFilter(filterClass: SearchFilter::class, properties: ['city' => 'exact', 'category' => 'exact', 'title' => 'partial'])]
#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]

    #[Groups('read')]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ApiProperty(identifier: true)]
    private ?int $id = null;

    #[Groups('read')]
    #[ORM\Column]
    private ?int $externalId = null;

    #[Groups('read')]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[Groups('read')]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[Groups('read')]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionSite = null;

    #[Groups('read')]
    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[Groups('read')]
    #[ORM\Column(length: 20)]
    private ?string $zipcode = null;

    #[Groups('read')]
    #[ORM\Column(length: 255)]
    private ?string $province = null;

    #[Groups('read')]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[Groups('read')]
    #[ORM\OneToMany(mappedBy: 'project', targetEntity: ConstructionType::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    /** @var Collection<int,ConstructionType> $constructionTypes */
    private Collection $constructionTypes;

    #[Groups('read')]
    #[ORM\Column(length: 255)]
    private string $category = 'Nieuwbouw';

    #[Groups('read')]
    #[ORM\Column]
    private array $diversen = [];

    #[Groups('read')]
    #[ORM\Column]
    private array $algemeen = [];

    #[Groups(['read', 'slug'])]
    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[Groups('read')]
    #[ORM\Column(nullable: true)]
    private ?array $media = null;

    #[ORM\Column]
    #[Ignore]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Ignore]
    private ?\DateTimeImmutable $updatedAt = null;

    #[Groups('read')]
    #[ORM\Column(length: 255)]
    private ?string $livingArea = null;

    #[Groups('read')]
    #[ORM\Column(length: 255)]
    private ?string $rooms = null;

    #[Groups('read')]
    #[ORM\Column(nullable: true)]
    private ?array $mainImage = null;

    #[Groups('read')]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $plot = null;

    #[ORM\Column]
    private bool $archived = false;

    #[ORM\Column(length: 32, columnDefinition: 'CHAR(32) NOT NULL')]
    #[Ignore]
    private ?string $mediaHash = null;

    public function __construct()
    {
        $this->constructionTypes = new ArrayCollection();
    }

    /**
     * @param Collection<int, ConstructionType> $newTypes
     */
    private function updateConstructionTypes(Collection $newTypes)
    {
        foreach ($newTypes as $newType) {
            /** @var ConstructionType|null $existingType */
            $existingType = $this->constructionTypes->filter(function ($type) use ($newType) {
                return $type->getExternalId() === $newType->getExternalId();
            })->first();

            if ($existingType) {
                $existingType->updateFromNewType($newType);
            } else {
                $newType->updateFromNewType($newType);
                $newType->setProject($this);
                $this->constructionTypes->add($newType);
            }
        }

        // Optionally, remove types that are no longer present
        foreach ($this->constructionTypes as $existingType) {
            if (!$newTypes->exists(function ($key, $type) use ($existingType) {
                return $type->getExternalId() === $existingType->getExternalId();
            })) {
                $this->constructionTypes->removeElement($existingType);
            }
        }
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    public function map(Project $newProperties)
    {
        $reflectionClass = new ReflectionClass($this);
        foreach ($reflectionClass->getMethods() as $method) {
            if (str_starts_with($method->getName(), 'set')) {
                $propertyName = substr($method->getName(), 3);
                $setMethod = 'set' . $propertyName;
                $getMethod = 'get' . $propertyName;
                if ($propertyName !== 'ConstructionTypes' && $propertyName !== 'Media' && $propertyName !== 'MainImage') {
                    $this->{$setMethod}($newProperties->{$getMethod}());
                }
                if ($propertyName === 'ConstructionTypes') {
                    $this->updateConstructionTypes($newProperties->{$getMethod}());
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

    public function getAlgemeen(): array
    {
        return $this->algemeen;
    }

    public function setAlgemeen(array $algemeen): static
    {
        $this->algemeen = $algemeen;

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

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): static
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getProvince(): ?string
    {
        return $this->province;
    }

    public function setProvince(string $province): static
    {
        $this->province = $province;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

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
        $this->setSlug($this->getCity().'-'.$this->getProvince().'-'.$this->getExternalId());

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDescriptionSite(): ?string
    {
        return $this->descriptionSite;
    }

    public function setDescriptionSite(?string $descriptionSite): static
    {
        $this->descriptionSite = $descriptionSite;

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

    /**
     * @return Collection<int, ConstructionType>
     */
    public function getConstructionTypes(): Collection
    {
        return $this->constructionTypes;
    }

    public function addConstructionType(ConstructionType $constructionType): static
    {
        if (!$this->constructionTypes->contains($constructionType)) {
            $this->constructionTypes->add($constructionType);
            $constructionType->setProject($this);
        }

        return $this;
    }

    public function removeConstructionType(ConstructionType $constructionType): static
    {
        if ($this->constructionTypes->removeElement($constructionType)) {
            // set the owning side to null (unless already changed)
            if ($constructionType->getProject() === $this) {
                $constructionType->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @param Collection<int, ConstructionType> $constructionTypes
     */
    public function setConstructionTypes(Collection $constructionTypes): static
    {
        $this->constructionTypes = $constructionTypes;

        return $this;
    }

    #[Groups('read')]
    public function getNumberOfObjects(): int
    {
        return ($this->getAlgemeen()['aantalBouwnummers'] ?? 0) + ($this->getAlgemeen()['aantalVrijeEenheden'] ?? 0);
    }

    #[Groups('read')]
    public function getNumberOfObjectsAvailable(): int
    {
        $numbersAvailable = 0;
        foreach ($this->getConstructionTypes()->toArray() as $type) {
            foreach ($type->getConstructionNumbers()->toArray() as $number) {
                if ($number->getStatus() === 'BESCHIKBAAR' || $number->getStatus() === 'ONDER_OPTIE') {
                    $numbersAvailable++;
                }
            }
        }
        return $numbersAvailable;
    }

    #[Groups('read')]
    public function getBuildYear(): ?string
    {
        $buildDateString = $this->getAlgemeen()['datumStartBouw'] ?? null;

        // get year from date string
        if (isset($buildDateString)) {
            $buildDate = new \DateTime($buildDateString);
            return $buildDate->format('Y');
        }

        return null;
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

    public function getLivingArea(): ?string
    {
        return $this->livingArea;
    }

    public function setLivingArea(string $livingArea): static
    {
        $this->livingArea = $livingArea;

        return $this;
    }

    public function getRooms(): ?string
    {
        return $this->rooms;
    }

    public function setRooms(string $rooms): static
    {
        $this->rooms = $rooms;

        return $this;
    }

    public function getMainImage(): ?array
    {
        return $this->mainImage;
    }

    public function setMainImage(?array $mainImage): static
    {
        $this->mainImage = $mainImage;

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

    #[Groups('read')]
    public function getPriceRange(): ?string
    {
        $minPrice = null;
        $maxPrice = null;

        $currencies = new ISOCurrencies();

        $numberFormatter = new \NumberFormatter('nl_NL', \NumberFormatter::CURRENCY);
        $numberFormatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, 0);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);

        $minPrice = match ($this->getAlgemeen()['koopOfHuur']) {
            'KOOP' => $this->getAlgemeen()['koopaanneemsomVanaf'],
            'HUUR' => $this->getAlgemeen()['huurprijsVanaf'],
        };

        $maxPrice = match ($this->getAlgemeen()['koopOfHuur']) {
            'KOOP' => $this->getAlgemeen()['koopaanneemsomTot'],
            'HUUR' => $this->getAlgemeen()['huurprijsTot'],
        };

        if (isset($minPrice) && isset($maxPrice) && $minPrice === $maxPrice) {
            return $moneyFormatter->format(Money::EUR($minPrice * 100));
        }

        if (isset($minPrice) && isset($maxPrice)) {
            return match($this->getAlgemeen()['koopOfHuur']) {
                'KOOP' => "Van {$moneyFormatter->format(Money::EUR($minPrice * 100))} tot {$moneyFormatter->format(Money::EUR($maxPrice * 100))} v.o.n.",
                'HUUR' => "Van {$moneyFormatter->format(Money::EUR($minPrice * 100))} tot {$moneyFormatter->format(Money::EUR($maxPrice * 100))} p.m.",
            };
        }

        return null;
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

    public function getMediaHash(): ?string
    {
        return $this->mediaHash;
    }

    public function setMediaHash(string $mediaHash): static
    {
        $this->mediaHash = $mediaHash;

        return $this;
    }
}
