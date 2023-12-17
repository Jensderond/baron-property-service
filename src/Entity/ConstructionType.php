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
use Symfony\Component\Serializer\Annotation\SerializedPath;

#[ORM\Entity(repositoryClass: ConstructionTypeRepository::class)]
#[ApiResource(operations: [new Get(name: "getConstructionTypeItem"), new GetCollection(name: "getConstructionTypeCollection")], graphQlOperations: [new Query(name: 'item_query'), new QueryCollection(name: 'collection_query', paginationType: 'page')])]
class ConstructionType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[SerializedPath('[id]')]
    private ?int $externalId = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[SerializedPath('[algemeen][omschrijving]')]
    private ?string $title = null;

    #[ORM\Column(nullable: true)]
    private ?array $algemeen = null;

    #[ORM\Column(nullable: true)]
    private ?array $media = null;

    #[ORM\Column(nullable: true)]
    private ?array $teksten = null;

    #[ORM\OneToMany(mappedBy: 'constructionType', targetEntity: ConstructionNumber::class, orphanRemoval: true, cascade: ['persist'])]
    #[SerializedPath('[bouwnummers]')]
    /** @var Collection<int, ConstructionNumber> $constructionNumbers */
    private Collection $constructionNumbers;

    #[ORM\ManyToOne(inversedBy: 'constructionTypes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project = null;

    public function __construct()
    {
        $this->constructionNumbers = new ArrayCollection();
    }

    public function map(ConstructionType $newProperties)
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

    public function getAlgemeen(): ?array
    {
        return $this->algemeen;
    }

    public function setAlgemeen(?array $algemeen): static
    {
        $this->algemeen = $algemeen;

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
}
