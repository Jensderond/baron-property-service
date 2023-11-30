<?php

namespace App\Contract;

interface AddressClientInterface
{
    public function getLatLngFromAddress(int $housenumber, string $street, string $city, string $country);
}
