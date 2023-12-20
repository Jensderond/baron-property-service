<?php

namespace App\Entity;

use App\Repository\PropertyDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PropertyDetailRepository::class)]
class PropertyDetail
{
    #[ORM\Id]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?array $buitenruimte = null;

    #[ORM\Column(nullable: true)]
    private ?array $etages = null;

    #[ORM\Column(nullable: true)]
    private ?array $overigOnroerendGoed = null;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEtages(): ?array
    {
        return $this->etages;
    }

    public function setEtages(?array $etages): static
    {
        $this->etages = $etages;

        return $this;
    }

    public function getOverigOnroerendGoed(): ?array
    {
        return $this->overigOnroerendGoed;
    }

    public function setOverigOnroerendGoed(?array $overigOnroerendGoed): static
    {
        $this->overigOnroerendGoed = $overigOnroerendGoed;

        return $this;
    }
}
