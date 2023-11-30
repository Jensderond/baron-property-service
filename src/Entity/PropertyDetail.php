<?php

namespace App\Entity;

use App\Repository\PropertyDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PropertyDetailRepository::class)]
class PropertyDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?LandRegistryData $kadaster = null;

    #[ORM\Column(nullable: true)]
    private ?array $buitenruimte = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKadaster(): ?LandRegistryData
    {
        return $this->kadaster;
    }

    public function setKadaster(?LandRegistryData $kadaster): static
    {
        $this->kadaster = $kadaster;

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
}
