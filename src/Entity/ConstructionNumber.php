<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ConstructionNumberRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\SerializedPath;

#[ORM\Entity(repositoryClass: ConstructionNumberRepository::class)]
#[ApiResource]
class ConstructionNumber
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[SerializedPath('[algemeen][omschrijving]')]
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
}
