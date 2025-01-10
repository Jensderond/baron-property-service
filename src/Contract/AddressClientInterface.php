<?php

namespace App\Contract;

interface AddressClientInterface
{
  public function getLatLngFromAddress(string|int $housenumber, string $street, string $city);
}
