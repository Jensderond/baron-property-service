<?php

namespace App\Entity;

use App\Repository\LandRegistryDataRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LandRegistryDataRepository::class)]
class LandRegistryData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $aandeel = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $afgekochtTot = null;

    #[ORM\Column(nullable: true)]
    private ?bool $afkoopoptie = null;

    #[ORM\Column(nullable: true)]
    private ?bool $eeuwigAfgekocht = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $eigendomssoort = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $einddatum = null;

    #[ORM\Column(nullable: true)]
    private ?int $erfpachtPerJaar = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $erfpachtduur = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $erfpachtgever = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $erfpachtprijsvorm = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $gemeente = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAandeel(): ?string
    {
        return $this->aandeel;
    }

    public function setAandeel(?string $aandeel): static
    {
        $this->aandeel = $aandeel;

        return $this;
    }

    public function getAfgekochtTot(): ?string
    {
        return $this->afgekochtTot;
    }

    public function setAfgekochtTot(?string $afgekochtTot): static
    {
        $this->afgekochtTot = $afgekochtTot;

        return $this;
    }

    public function isAfkoopoptie(): ?bool
    {
        return $this->afkoopoptie;
    }

    public function setAfkoopoptie(?bool $afkoopoptie): static
    {
        $this->afkoopoptie = $afkoopoptie;

        return $this;
    }

    public function isEeuwigAfgekocht(): ?bool
    {
        return $this->eeuwigAfgekocht;
    }

    public function setEeuwigAfgekocht(?bool $eeuwigAfgekocht): static
    {
        $this->eeuwigAfgekocht = $eeuwigAfgekocht;

        return $this;
    }

    public function getEigendomssoort(): ?string
    {
        return $this->eigendomssoort;
    }

    public function setEigendomssoort(?string $eigendomssoort): static
    {
        $this->eigendomssoort = $eigendomssoort;

        return $this;
    }

    public function getEinddatum(): ?\DateTimeInterface
    {
        return $this->einddatum;
    }

    public function setEinddatum(?\DateTimeInterface $einddatum): static
    {
        $this->einddatum = $einddatum;

        return $this;
    }

    public function getErfpachtPerJaar(): ?int
    {
        return $this->erfpachtPerJaar;
    }

    public function setErfpachtPerJaar(?int $erfpachtPerJaar): static
    {
        $this->erfpachtPerJaar = $erfpachtPerJaar;

        return $this;
    }

    public function getErfpachtduur(): ?string
    {
        return $this->erfpachtduur;
    }

    public function setErfpachtduur(?string $erfpachtduur): static
    {
        $this->erfpachtduur = $erfpachtduur;

        return $this;
    }

    public function getErfpachtgever(): ?string
    {
        return $this->erfpachtgever;
    }

    public function setErfpachtgever(?string $erfpachtgever): static
    {
        $this->erfpachtgever = $erfpachtgever;

        return $this;
    }

    public function getErfpachtprijsvorm(): ?string
    {
        return $this->erfpachtprijsvorm;
    }

    public function setErfpachtprijsvorm(?string $erfpachtprijsvorm): static
    {
        $this->erfpachtprijsvorm = $erfpachtprijsvorm;

        return $this;
    }

    public function getGemeente(): ?string
    {
        return $this->gemeente;
    }

    public function setGemeente(?string $gemeente): static
    {
        $this->gemeente = $gemeente;

        return $this;
    }
}
