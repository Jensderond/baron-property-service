<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Cocur\Slugify\Slugify;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use ReflectionClass;

/**
 * A property.
 */
#[ApiResource(
    collectionOperations: [
        'get' => ['method' => 'get'],
    ],
    itemOperations: [
        'get' => ['method' => 'get'],
    ],
)]
#[ApiFilter(DateFilter::class, properties: ['created', 'updated'])]
#[ApiFilter(SearchFilter::class, properties: ['city' => 'exact', 'category' => 'exact', 'type' => 'exact', 'archived' => 'exact', 'status' => 'exact', 'address' => 'partial'])]
#[ApiFilter(RangeFilter::class, properties: ['price', 'rooms', 'plot_surface'])]
#[Entity]
class Property
{
    #[Id]
    #[Column(type: 'integer')]
    private $id;

    #[Column(type: 'string', length: 100, nullable: true)]
    private $category;

    #[Column(type: 'boolean')]
    private $archived;

    #[Column(type: 'integer', nullable: true)]
    private $build_year;

    #[Column(type: 'string', length: 255, nullable: true)]
    private $build_period;

    #[Column(type: 'integer', nullable: true)]
    private $living_space;

    #[Column(type: 'integer', nullable: true)]
    private $plot_surface;

    #[Column(type: 'integer', nullable: true)]
    private $volume;

    #[Column(type: 'integer', nullable: true)]
    private $rooms;

    #[Column(type: 'integer', nullable: true)]
    private $bedrooms;

    #[Column(type: 'string', length: 100)]
    private $status;

    #[Column(type: 'integer', nullable: true)]
    private $other_indoor_space;

    #[Column(type: 'integer', nullable: true)]
    private $external_storage;

    #[Column(type: 'datetime')]
    private $created;

    #[Column(type: 'datetime')]
    private $updated;

    #[Column(type: 'string', length: 50)]
    private $registration_type;

    #[Column(type: 'boolean', nullable: true)]
    private $sale;

    #[Column(type: 'boolean', nullable: true)]
    private $rent;

    #[Column(type: 'string', length: 255)]
    private $category_rename;

    #[Column(type: 'integer', nullable: true)]
    private $price;

    #[Column(type: 'string', length: 255, nullable: true)]
    private $address;

    #[Column(type: 'string', length: 255, nullable: true)]
    private $street_address;

    #[Column(type: 'string', length: 255, nullable: true)]
    private $house_number;

    #[Column(type: 'string', length: 255, nullable: true)]
    private $house_number_addition;

    #[Column(type: 'string', length: 255, nullable: true)]
    private $street;

    #[Column(type: 'string', length: 255, nullable: true)]
    private $zip;

    #[Column(type: 'string', length: 255, nullable: true)]
    private $city;

    #[Column(type: 'string', length: 255, nullable: true)]
    private $latitude;

    #[Column(type: 'string', length: 255, nullable: true)]
    private $longitude;

    #[Column(type: 'string', length: 255, nullable: true)]
    private $acceptance;

    #[Column(type: 'string', length: 10, nullable: true)]
    private $energy_class;

    #[Column(type: 'string', length: 255, nullable: true)]
    private $type;

    #[Column(type: 'string', length: 255, nullable: true)]
    private $subtype;

    #[Column(type: 'boolean')]
    private $new_construction;

    #[Column(type: 'boolean')]
    private $pets_allowed;

    #[Column(type: 'array', nullable: true)]
    private $images = [];

    #[Column(type: 'string', length: 255, nullable: true)]
    private $image;

    #[Column(type: 'object', nullable: true)]
    private $titles;

    #[Column(type: 'object', nullable: true)]
    private $meta_keywords;

    #[Column(type: 'object', nullable: true)]
    private $meta_descriptions;

    #[Column(type: 'string', length: 255, nullable: true)]
    private $price_type_sale;

    #[Column(type: 'string', length: 255)]
    private $slug;

    #[OneToMany(mappedBy: 'property', targetEntity: Video::class, orphanRemoval: true, cascade: ['persist'])]
    private $videos;

    #[Column(type: 'text')]
    private $description_nl;

    #[Column(type: 'string', length: 20, nullable: true)]
    private $commercial_manager_main_phone;

    #[Column(type: 'string', length: 20, nullable: true)]
    private $commercial_manager_whatsapp;

    #[Column(type: 'text', nullable: true)]
    private $external_plans;

    #[Column(type: 'text', nullable: true)]
    private $external_panoramas;

    #[OneToMany(mappedBy: 'property', targetEntity: Plan::class, orphanRemoval: true, cascade: ['persist'])]
    private $plans;

    #[Column(type: 'integer', nullable: true)]
    private $rental_price;

    #[Column(type: 'integer', nullable: true)]
    private $deposit;

    #[Column(type: 'string', length: 20, nullable: true)]
    private $rental_condition;

    #[Column(type: 'string', length: 20, nullable: true)]
    private $availability;

    #[Column(type: 'date', nullable: true)]
    private $available_from;

    #[Column(type: 'date', nullable: true)]
    private $rented_till;

    #[Column(type: 'integer', nullable: true)]
    private $min_contract_length;

    #[Column(type: 'integer', nullable: true)]
    private $contract_length;

    #[Column(type: 'float', nullable: true)]
    private $service_costs;

    #[Column(type: 'float', nullable: true)]
    private $owners_contribution_community;

    public function __construct()
    {
        $this->videos = new ArrayCollection();
        $this->plans = new ArrayCollection();
    }

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

    public function setArchived(string|bool $archived): self
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

    public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    public function setCreated(string|DateTime $created): self
    {
        if (is_string($created)) {
            $created = DateTime::createFromFormat('Y-m-d H:i:s', $created);
        }
        $this->created = $created;

        return $this;
    }

    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    public function setUpdated(string|DateTime $updated): self
    {
        if (is_string($updated)) {
            $updated = DateTime::createFromFormat('Y-m-d H:i:s', $updated);
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        if (!isset($slug)) {
            $this->slug = '';

            return $this;
        }

        $slugify = new Slugify();

        $this->slug = $slugify->slugify($slug);

        return $this;
    }

    /**
     * @return Collection|Video[]
     */
    public function getVideos(): ?Collection
    {
        return $this->videos;
    }

    public function setVideos(ArrayCollection $videos): self
    {
        foreach ($this->videos as $video) {
            $found = $videos->exists(function ($key, $item) use ($video) {
                return $item->getCode() === $video->getCode();
            });

            if (!$found) {
                $this->removeVideo($video);
            }
        }

        foreach ($videos as $video) {
            if ($video instanceof Video) {
                $found = $this->videos->exists(function ($key, $item) use ($video) {
                    return $item->getCode() === $video->getCode();
                });

                if (!$found) {
                    $this->addVideo($video);
                }
            }
        }

        return $this;
    }

    public function addVideo(Video $video): self
    {
        $this->videos[] = $video;
        $video->setProperty($this);

        return $this;
    }

    public function removeVideo(Video $video): self
    {
        if ($this->videos->removeElement($video)) {
            // set the owning side to null (unless already changed)
            if ($video->getProperty() === $this) {
                $video->setProperty(null);
            }
        }

        return $this;
    }

    public function getDescriptionNl(): ?string
    {
        return $this->description_nl;
    }

    public function setDescriptionNl(string $description_nl): self
    {
        $this->description_nl = $description_nl;

        return $this;
    }

    public function getCommercialManagerMainPhone(): ?string
    {
        return $this->commercial_manager_main_phone;
    }

    public function setCommercialManagerMainPhone(?string $commercial_manager_main_phone): self
    {
        $this->commercial_manager_main_phone = $commercial_manager_main_phone;

        return $this;
    }

    public function getCommercialManagerWhatsapp(): ?string
    {
        return $this->commercial_manager_whatsapp;
    }

    public function setCommercialManagerWhatsapp(?string $commercial_manager_whatsapp): self
    {
        $this->commercial_manager_whatsapp = $commercial_manager_whatsapp;

        return $this;
    }

    public function map(Property $newProperties)
    {
        $reflectionClass = new ReflectionClass($this);
        foreach ($reflectionClass->getMethods() as $method) {
            if ('set' === substr($method->getName(), 0, 3)) {
                $setMethod = 'set'.substr($method->getName(), 3);
                $getMethod = 'get'.substr($method->getName(), 3);
                $this->$setMethod($newProperties->$getMethod());
            }
        }
    }

    public function getExternalPlans(): ?string
    {
        return $this->external_plans;
    }

    public function setExternalPlans(?string $external_plans): self
    {
        $this->external_plans = $external_plans;

        return $this;
    }

    public function getExternalPanoramas(): ?string
    {
        return $this->external_panoramas;
    }

    public function setExternalPanoramas(?string $external_panoramas): self
    {
        $this->external_panoramas = $external_panoramas;

        return $this;
    }

    /**
     * @return Collection|Plan[]
     */
    public function getPlans(): Collection
    {
        return $this->plans;
    }

    public function setPlans(ArrayCollection $plans): self
    {
        foreach ($this->plans as $plan) {
            $found = $plans->exists(function ($key, $item) use ($plan) {
                return $item->getId() === $plan->getId();
            });

            if (!$found) {
                $this->removePlan($plan);
            }
        }

        foreach ($plans as $plan) {
            if ($plan instanceof Plan) {
                $found = $this->plans->exists(function ($key, $item) use ($plan) {
                    return $item->getId() === $plan->getId();
                });

                if (!$found) {
                    $this->addPlan($plan);
                }
            }
        }

        return $this;
    }

    public function addPlan(Plan $plan): self
    {
        $this->plans[] = $plan;
        $plan->setProperty($this);

        return $this;
    }

    public function removePlan(Plan $plan): self
    {
        if ($this->plans->removeElement($plan)) {
            // set the owning side to null (unless already changed)
            if ($plan->getProperty() === $this) {
                $plan->setProperty(null);
            }
        }

        return $this;
    }

    public function getRentalPrice(): ?int
    {
        return $this->rental_price;
    }

    public function setRentalPrice(?int $rental_price): self
    {
        $this->rental_price = $rental_price;

        return $this;
    }

    public function getDeposit(): ?int
    {
        return $this->deposit;
    }

    public function setDeposit(?int $deposit): self
    {
        $this->deposit = $deposit;

        return $this;
    }

    public function getRentalCondition(): ?string
    {
        return $this->rental_condition;
    }

    public function setRentalCondition(?string $rental_condition): self
    {
        $this->rental_condition = $rental_condition;

        return $this;
    }

    public function getAvailability(): ?string
    {
        return $this->availability;
    }

    public function setAvailability(?string $availability): self
    {
        $this->availability = $availability;

        return $this;
    }

    public function getAvailableFrom(): ?\DateTimeInterface
    {
        return $this->available_from;
    }

    public function setAvailableFrom(?\DateTimeInterface $available_from): self
    {
        $this->available_from = $available_from;

        return $this;
    }

    public function getRentedTill(): ?\DateTimeInterface
    {
        return $this->rented_till;
    }

    public function setRentedTill(?\DateTimeInterface $rented_till): self
    {
        $this->rented_till = $rented_till;

        return $this;
    }

    public function getMinContractLength(): ?int
    {
        return $this->min_contract_length;
    }

    public function setMinContractLength(?int $min_contract_length): self
    {
        $this->min_contract_length = $min_contract_length;

        return $this;
    }

    public function getContractLength(): ?int
    {
        return $this->contract_length;
    }

    public function setContractLength(?int $contract_length): self
    {
        $this->contract_length = $contract_length;

        return $this;
    }

    public function getServiceCosts(): ?float
    {
        return $this->service_costs;
    }

    public function setServiceCosts(?float $service_costs): self
    {
        $this->service_costs = $service_costs;

        return $this;
    }

    public function getOwnersContributionCommunity(): ?float
    {
        return $this->owners_contribution_community;
    }

    public function setOwnersContributionCommunity(?float $owners_contribution_community): self
    {
        $this->owners_contribution_community = $owners_contribution_community;

        return $this;
    }
}
