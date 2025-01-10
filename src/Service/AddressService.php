<?php

namespace App\Service;

use App\Contract\AddressClientInterface;

class AddressService implements AddressClientInterface
{
  public function __construct(private readonly AddressClientInterface $client) {}

  /**
   * @return array{
   *   'lat': float,
   *   'lng': float,
   * }
   */
  public function getLatLngFromAddress(mixed $housenumber, string $street, string $city): ?array
  {
    $address = $this->client->getLatLngFromAddress($housenumber, $street, $city);
    $data = json_decode($address, true);

    $value = [
      'lat' => '',
      'lng' => '',
    ];

    try {
      $value = [
        'lat' => (float) $data[0]['lat'],
        'lng' => (float) $data[0]['lon'],
      ];

      return $value;
    } catch (\Exception $e) {
      return null;
    }
  }
}
