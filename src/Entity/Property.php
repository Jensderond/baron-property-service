<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * A property.
 * @ORM\Entity
 */
#[ApiResource]
#[ApiFilter(DateFilter::class, properties: ["created", "updated"])]
#[ApiFilter(SearchFilter::class, properties: ["category" => "exact", "archived" => "exact", "status" => "exact", "description" => "partial"])]
#[ApiFilter(RangeFilter::class, properties: ["price"])]
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

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $street_address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $house_number;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $house_number_addition;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $street;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $zip;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $latitude;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $longitude;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $acceptance;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $energy_class;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subtype;

    /**
     * @ORM\Column(type="boolean")
     */
    private $new_construction;

    /**
     * @ORM\Column(type="boolean")
     */
    private $pets_allowed;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $images = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="object", nullable=true)
     */
    private $titles;

    /**
     * @ORM\Column(type="object", nullable=true)
     */
    private $meta_keywords;

    /**
     * @ORM\Column(type="object", nullable=true)
     */
    private $meta_descriptions;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $price_type_sale;

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

    public function setArchived($archived): self
    {
        if (is_string($archived)) {
            $archived = filter_var($archived, FILTER_VALIDATE_BOOLEAN);
        }

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
        foreach($reflectionClass->getMethods() as $method) {
            if (substr($method->getName(), 0, 3) === 'set') {
                $setMethod = 'set' . substr($method->getName(), 3);
                $getMethod = 'get' . substr($method->getName(), 3);
                $this->$setMethod($newProperties->$getMethod());
            }
        }
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getStreetAddress(): ?string
    {
        return $this->street_address;
    }

    public function setStreetAddress(?string $street_address): self
    {
        $this->street_address = $street_address;

        return $this;
    }

    public function getHouseNumber(): ?string
    {
        return $this->house_number;
    }

    public function setHouseNumber(?string $house_number): self
    {
        $this->house_number = $house_number;

        return $this;
    }

    public function getHouseNumberAddition(): ?string
    {
        return $this->house_number_addition;
    }

    public function setHouseNumberAddition(?string $house_number_addition): self
    {
        $this->house_number_addition = $house_number_addition;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function setZip(?string $zip): self
    {
        $this->zip = $zip;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getAcceptance(): ?string
    {
        return $this->acceptance;
    }

    public function setAcceptance(?string $acceptance): self
    {
        $this->acceptance = $acceptance;

        return $this;
    }

    public function getEnergyClass(): ?string
    {
        return $this->energy_class;
    }

    public function setEnergyClass(?string $energy_class): self
    {
        $this->energy_class = $energy_class;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSubtype(): ?string
    {
        return $this->subtype;
    }

    public function setSubtype(?string $subtype): self
    {
        $this->subtype = $subtype;

        return $this;
    }

    public function getNewConstruction(): ?bool
    {
        return $this->new_construction;
    }

    public function setNewConstruction(bool $new_construction): self
    {
        $this->new_construction = $new_construction;

        return $this;
    }

    public function getPetsAllowed(): ?bool
    {
        return $this->pets_allowed;
    }

    public function setPetsAllowed(bool $pets_allowed): self
    {
        $this->pets_allowed = $pets_allowed;

        return $this;
    }

    public function getImages(): ?array
    {
        return $this->images;
    }

    public function setImages(?array $images): self
    {
        $this->images = $images;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getTitles()
    {
        return $this->titles;
    }

    public function setTitles($titles): self
    {
        $this->titles = $titles;

        return $this;
    }

    public function getMetaKeywords()
    {
        return $this->meta_keywords;
    }

    public function setMetaKeywords($meta_keywords): self
    {
        $this->meta_keywords = $meta_keywords;

        return $this;
    }

    public function getMetaDescriptions()
    {
        return $this->meta_descriptions;
    }

    public function setMetaDescriptions($meta_descriptions): self
    {
        $this->meta_descriptions = $meta_descriptions;

        return $this;
    }

    public function getPriceTypeSale(): ?string
    {
        return $this->price_type_sale;
    }

    public function setPriceTypeSale(?string $price_type_sale): self
    {
        $this->price_type_sale = $price_type_sale;

        return $this;
    }
}
