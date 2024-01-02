<?php

namespace App\Entity;

use ApiPlatform\Metadata\GraphQl\QueryCollection;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\ConstructionTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ReflectionClass;
use Symfony\Component\Serializer\Annotation\Groups;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;

#[ORM\Entity(repositoryClass: ConstructionTypeRepository::class)]
#[ApiResource(operations: [new Get(name: "getConstructionTypeItem"), new GetCollection(name: "getConstructionTypeCollection")], graphQlOperations: [new Query(name: 'item_query'), new QueryCollection(name: 'collection_query', paginationType: 'page')])]
class ConstructionType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $externalId = null;

    #[Groups('read')]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(nullable: true)]
    private ?array $media = null;

    #[ORM\Column(nullable: true)]
    private ?array $teksten = null;

    #[Groups('read')]
    #[ORM\OneToMany(mappedBy: 'constructionType', targetEntity: ConstructionNumber::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    /** @var Collection<int, ConstructionNumber> $constructionNumbers */
    private Collection $constructionNumbers;

    #[ORM\ManyToOne(inversedBy: 'constructionTypes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

    #[Groups('read')]
    #[ORM\Column(length: 255)]
    private ?string $livingArea = null;

    #[ORM\Column(nullable: true)]
    private ?array $algemeen = null;

    #[Groups('read')]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type = null;

    #[Groups('read')]
    #[ORM\Column(nullable: true)]
    private ?int $rooms = null;

    #[Groups('read')]
    #[ORM\Column(nullable: true)]
    private ?array $mainImage = [];

    public function __construct()
    {
        $this->constructionNumbers = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    public function map(ConstructionType $newProperties)
    {
        $reflectionClass = new ReflectionClass($this);
        foreach ($reflectionClass->getMethods() as $method) {
            if (str_starts_with($method->getName(), 'set')) {
                $propertyName = substr($method->getName(), 3);
                $setMethod = 'set' . $propertyName;
                $getMethod = 'get' . $propertyName;
                if ($propertyName !== 'ConstructionNumbers' && $propertyName !== 'Project') {
                    $this->{$setMethod}($newProperties->{$getMethod}());
                } elseif ($propertyName !== 'Project') {
                    $this->updateConstructionNumbers($newProperties->{$getMethod}());
                }
            }
        }
    }

    public function updateFromNewType(ConstructionType $newType)
    {
        $this->map($newType);
        $this->updateConstructionNumbers($newType->getConstructionNumbers());
    }

    /**
     * @param Collection<int, ConstructionNumber> $newNumbers
     */
    private function updateConstructionNumbers(Collection $newNumbers)
    {
        foreach ($newNumbers as $newNumber) {
            /** @var ConstructionNumber|null $existingNumber */
            $existingNumber = $this->constructionNumbers->filter(function ($number) use ($newNumber) {
                return $number->getExternalId() === $newNumber->getExternalId();
            })->first();

            if ($existingNumber) {
                $existingNumber->updateFromNewNumber($newNumber);
            } else {
                $newNumber->setConstructionType($this);
                $this->constructionNumbers->add($newNumber);
            }
        }

        foreach ($this->constructionNumbers as $existingNumber) {
            if (!$newNumbers->exists(function ($key, $number) use ($existingNumber) {
                return $number->getExternalId() === $existingNumber->getExternalId();
            })) {
                $this->constructionNumbers->removeElement($existingNumber);
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

    /**
     * @return Collection<int, ConstructionNumber>
     */
    public function getConstructionNumbers(): Collection
    {
        return $this->constructionNumbers;
    }

    public function addConstructionNumber(ConstructionNumber $constructionNumber): static
    {
        if (!$this->constructionNumbers->contains($constructionNumber)) {
            $this->constructionNumbers->add($constructionNumber);
            $constructionNumber->setConstructionType($this);
        }

        return $this;
    }

    public function removeConstructionNumber(ConstructionNumber $constructionNumber): static
    {
        if ($this->constructionNumbers->removeElement($constructionNumber)) {
            // set the owning side to null (unless already changed)
            if ($constructionNumber->getConstructionType() === $this) {
                $constructionNumber->setConstructionType(null);
            }
        }

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): static
    {
        $this->project = $project;

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

    public function getLivingArea(): ?string
    {
        return $this->livingArea;
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

        $minPrice = match ($this->getAlgemeen()['koopHuur']) {
            'KOOP' => $this->getAlgemeen()['koopaanneemsomVanaf'],
            'HUUR' => $this->getAlgemeen()['huurprijsVanaf'],
        };

        $maxPrice = match ($this->getAlgemeen()['koopHuur']) {
            'KOOP' => $this->getAlgemeen()['koopaanneemsomTot'],
            'HUUR' => $this->getAlgemeen()['huurprijsTot'],
        };

        if (isset($minPrice) && isset($maxPrice) && $minPrice === $maxPrice) {
            return $moneyFormatter->format(new Money($minPrice * 100, new Currency('EUR')));
        }

        if (isset($minPrice) && isset($maxPrice)) {
            return match($this->getAlgemeen()['koopHuur']) {
                'KOOP' => "Van {$moneyFormatter->format(new Money($minPrice * 100, new Currency('EUR')))} tot {$moneyFormatter->format(new Money($maxPrice * 100, new Currency('EUR')))} v.o.n.",
                'HUUR' => "Vanaf {$moneyFormatter->format(new Money($minPrice * 100, new Currency('EUR')))} per maand",
            };
        }

        return null;
    }

    public function setLivingArea(string $livingArea): static
    {
        $this->livingArea = $livingArea;

        return $this;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

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

    public function getMainImage(): ?array
    {
        return $this->mainImage;
    }

    public function setMainImage(array $mainImage): static
    {
        $this->mainImage = $mainImage;

        return $this;
    }
}
