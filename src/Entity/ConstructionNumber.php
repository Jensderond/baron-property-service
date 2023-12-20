<?php

namespace App\Entity;

use ApiPlatform\Metadata\GraphQl\QueryCollection;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\ConstructionNumberRepository;
use Cocur\Slugify\Slugify;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ReflectionClass;
use Symfony\Component\Serializer\Annotation\SerializedPath;

#[ORM\Entity(repositoryClass: ConstructionNumberRepository::class)]
#[ApiResource(operations: [new Get(name: "getConstructionNumberItem"), new GetCollection(name: "getConstructionNumberCollection")], graphQlOperations: [new Query(name: 'item_query'), new QueryCollection(name: 'collection_query', paginationType: 'page')])]
class ConstructionNumber
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[SerializedPath('[adres][straat]')]
    private ?string $title = null;

    #[ORM\Column(nullable: true)]
    private ?array $algemeen = null;

    #[ORM\Column]
    #[SerializedPath('[id]')]
    private ?int $externalId = null;

    #[ORM\Column(nullable: true)]
    private ?array $media = null;

    #[ORM\Column(nullable: true)]
    #[SerializedPath('[adres]')]
    private ?array $address = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[SerializedPath('[teksten][aanbiedingstekst]')]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?array $teksten = null;

    #[ORM\Column(nullable: true)]
    private ?array $diversen = null;

    #[ORM\ManyToOne(inversedBy: 'constructionNumbers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ConstructionType $constructionType = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    public function map(ConstructionNumber $newProperties)
    {
        $reflectionClass = new ReflectionClass($this);
        foreach ($reflectionClass->getMethods() as $method) {
            if (str_starts_with($method->getName(), 'set')) {
                $propertyName = substr($method->getName(), 3);
                $setMethod = 'set' . $propertyName;
                $getMethod = 'get' . $propertyName;
                if ($propertyName !== 'ConstructionType') {
                    $this->{$setMethod}($newProperties->{$getMethod}());
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
}
