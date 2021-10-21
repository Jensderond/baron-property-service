<?php

namespace App\Entity;

use App\Repository\PropertyRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PropertyRepository::class)
 */
class Property
{
    /**
     * @ORM\Id
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

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $registration_type;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $sale;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $rent;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $category_rename;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $updateHash;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
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

    public function getCreated(): ?\DateTime
    {
        return $this->created;
    }


    /**
     * @param string|\DateTime $created
     *
     * @return $this
     */
    public function setCreated($created): self
    {
        if (is_string($created)) {
            $created = \DateTime::createFromFormat('Y-m-d H:i:s', $created);
        }
        $this->created = $created;

        return $this;
    }

    public function getUpdated(): ?\DateTime
    {
        return $this->updated;
    }

    /**
     * @param string|\DateTime $updated
     *
     * @return $this
     */
    public function setUpdated( $updated): self
    {
        if (is_string($updated)) {
            $updated = \DateTime::createFromFormat('Y-m-d H:i:s', $updated);
        }
        $this->updated = $updated;

        return $this;
    }

    public function getRegistrationType(): ?string
    {
        return $this->registration_type;
    }

    public function setRegistrationType(string $registration_type): self
    {
        $this->registration_type = $registration_type;

        return $this;
    }

    public function getSale(): ?bool
    {
        return $this->sale;
    }

    public function setSale(?bool $sale): self
    {
        $this->sale = $sale;

        return $this;
    }

    public function getRent(): ?bool
    {
        return $this->rent;
    }

    public function setRent(?bool $rent): self
    {
        $this->rent = $rent;

        return $this;
    }

    public function getCategoryRename(): ?string
    {
        return $this->category_rename;
    }

    public function setCategoryRename(string $category_rename): self
    {
        $this->category_rename = $category_rename;

        return $this;
    }

    public function getUpdateHash(): ?string
    {
        return $this->updateHash;
    }

    public function setUpdateHash(?string $updateHash): self
    {
        $this->updateHash = $updateHash;

        return $this;
    }

    public function map(Property $newProperties)
    {
        $reflectionClass = new \ReflectionClass($this);
        foreach($reflectionClass->getMethods() as $method) { // setUpdateHash
            if (substr($method->getName(), 0, 3) === 'set') {
                $setMethod = 'set' . substr($method->getName(), 3);
                $getMethod = 'get' . substr($method->getName(), 3); // getUpdateHash
                $this->$setMethod($newProperties->$getMethod());
            }
        }
    }
}
