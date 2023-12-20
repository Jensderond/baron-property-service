<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Cocur\Slugify\Slugify;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\SerializedPath;
use Doctrine\ORM\Mapping as ORM;
use ReflectionClass;

#[ApiResource(operations: [new Get(name: "getProjectItem"), new GetCollection(name: "getProjectCollection")], graphQlOperations: [new Query(name: 'item_query'), new QueryCollection(name: 'collection_query', paginationType: 'page')])]
#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ApiProperty(identifier: true)]
    private ?int $id = null;

    #[ORM\Column]
    #[SerializedPath('[project][id]')]
    private ?int $externalId = null;

    #[ORM\Column(length: 255)]
    #[SerializedPath('[project][algemeen][omschrijving]')]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[SerializedPath('[teksten][aanbiedingstekst]')]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[SerializedPath('[teksten][eigenSiteTekst]')]
    private ?string $descriptionSite = null;

    #[ORM\Column(length: 255)]
    #[SerializedPath('[project][algemeen][plaats]')]
    private ?string $city = null;

    #[ORM\Column(length: 20)]
    #[SerializedPath('[project][algemeen][postcode]')]
    private ?string $zipcode = null;

    #[ORM\Column(length: 255)]
    #[SerializedPath('[project][algemeen][provincie]')]
    private ?string $province = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[SerializedPath('[project][diversen][status]')]
    private ?string $status = null;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: ConstructionType::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    #[SerializedPath('[bouwtypen]')]
    /** @var Collection<int,ConstructionType> $constructionTypes */
    private Collection $constructionTypes;

    #[ORM\Column(length: 255)]
    private ?string $category = 'Nieuwbouw';

    #[ORM\Column]
    #[SerializedPath('[project][diversen]')]
    private array $diversen = [];

    #[ORM\Column]
    #[SerializedPath('[project][algemeen]')]
    private array $algemeen = [];

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(nullable: true)]
    #[SerializedPath('[project][media]')]
    private ?array $media = null;

    public function __construct()
    {
        $this->constructionTypes = new ArrayCollection();
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
                if ($propertyName !== 'ConstructionTypes') {
                    $this->{$setMethod}($newProperties->{$getMethod}());
                } else {
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

    public function getImage(): ?string
    {
        $mainImage = array_filter($this->getMedia(), function ($media) {
            return $media['soort'] === 'HOOFDFOTO';
        });

        $mainImage = array_values($mainImage);

        if (isset($mainImage[0])) {
            return $mainImage[0]['link'];
        } elseif (isset($this->getMedia()[0])) {
            return $this->getMedia()[0]['link'];
        }

        return null;
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

    public function getNumberOfObjects(): int
    {
        return ($this->getAlgemeen()['aantalBouwnummers'] ?? 0) + ($this->getAlgemeen()['aantalVrijeEenheden'] ?? 0);
    }

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

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }
}
