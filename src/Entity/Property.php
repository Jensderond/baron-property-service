<?php

namespace App\Entity;

use App\Repository\PropertyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PropertyRepository::class)
 */
class Property
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $category;

    /**
     * @ORM\Column(type="boolean")
     */
    private $archived;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $build_year;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $build_period;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $living_space;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $plot_surface;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $volume;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rooms;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $bedrooms;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $bathrooms;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $status;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $other_indoor_space;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $external_storage;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getArchived(): ?bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): self
    {
        $this->archived = $archived;

        return $this;
    }

    public function getBuildYear(): ?int
    {
        return $this->build_year;
    }

    public function setBuildYear(?int $build_year): self
    {
        $this->build_year = $build_year;

        return $this;
    }

    public function getBuildPeriod(): ?string
    {
        return $this->build_period;
    }

    public function setBuildPeriod(?string $build_period): self
    {
        $this->build_period = $build_period;

        return $this;
    }

    public function getLivingSpace(): ?int
    {
        return $this->living_space;
    }

    public function setLivingSpace(?int $living_space): self
    {
        $this->living_space = $living_space;

        return $this;
    }

    public function getPlotSurface(): ?int
    {
        return $this->plot_surface;
    }

    public function setPlotSurface(?int $plot_surface): self
    {
        $this->plot_surface = $plot_surface;

        return $this;
    }

    public function getVolume(): ?int
    {
        return $this->volume;
    }

    public function setVolume(?int $volume): self
    {
        $this->volume = $volume;

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(?int $rooms): self
    {
        $this->rooms = $rooms;

        return $this;
    }

    public function getBedrooms(): ?int
    {
        return $this->bedrooms;
    }

    public function setBedrooms(?int $bedrooms): self
    {
        $this->bedrooms = $bedrooms;

        return $this;
    }

    public function getBathrooms(): ?int
    {
        return $this->bathrooms;
    }

    public function setBathrooms(?int $bathrooms): self
    {
        $this->bathrooms = $bathrooms;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getOtherIndoorSpace(): ?int
    {
        return $this->other_indoor_space;
    }

    public function setOtherIndoorSpace(?int $other_indoor_space): self
    {
        $this->other_indoor_space = $other_indoor_space;

        return $this;
    }

    public function getExternalStorage(): ?int
    {
        return $this->external_storage;
    }

    public function setExternalStorage(?int $external_storage): self
    {
        $this->external_storage = $external_storage;

        return $this;
    }
}
