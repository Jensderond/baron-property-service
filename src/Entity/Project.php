<?php

namespace App\Entity;

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

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: ConstructionType::class, orphanRemoval: true, cascade: ['persist'])]
    #[SerializedPath('[bouwtypen]')]
    /** @var Collection<int,ConstructionType> $constructionTypes */
    private Collection $constructionTypes;

    #[ORM\Column]
    #[SerializedPath('[project][diversen]')]
    private array $diversen = [];

    #[ORM\Column]
    #[SerializedPath('[project][algemeen]')]
    private array $algemeen = [];

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(nullable: true)]
    private ?array $media = null;

    public function __construct()
    {
        $this->constructionTypes = new ArrayCollection();
    }

    public function map(Project $newProperties)
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
}
