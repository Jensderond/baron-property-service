<?php

namespace App\Model;

class Address
{
    private string $house_number;
    private string $road;
    private string $hamlet;
    private string $town;
    private string $village;
    private string $city;
    private string $state_district;
    private string $state;
    private string $postcode;
    private string $country;
    private string $country_code;

    public function __construct(
    ) {
    }

    public function getHouseNumber(): string
    {
        return $this->house_number;
    }

    public function setHouseNumber(string $house_number): void
    {
        $this->house_number = $house_number;
    }

    public function getRoad(): string
    {
        return $this->road;
    }

    public function setRoad(string $road): void
    {
        $this->road = $road;
    }

    public function getHamlet(): string
    {
        return $this->hamlet;
    }

    public function setHamlet(string $hamlet): void
    {
        $this->hamlet = $hamlet;
    }

    public function getTown(): string
    {
        return $this->town;
    }

    public function setTown(string $town): void
    {
        $this->town = $town;
    }

    public function getVillage(): string
    {
        return $this->village;
    }

    public function setVillage(string $village): void
    {
        $this->village = $village;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getStateDistrict(): string
    {
        return $this->state_district;
    }

    public function setStateDistrict(string $state_district): void
    {
        $this->state_district = $state_district;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getPostcode(): string
    {
        return $this->postcode;
    }

    public function setPostcode(string $postcode): void
    {
        $this->postcode = $postcode;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    public function getCountryCode(): string
    {
        return $this->country_code;
    }

    public function setCountryCode(string $country_code): void
    {
        $this->country_code = $country_code;
    }
}
