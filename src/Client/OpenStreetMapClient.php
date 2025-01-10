<?php

namespace App\Client;

use App\Contract\AddressClientInterface;
use Error;
use Symfony\Contracts\HttpClient\Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenStreetMapClient implements AddressClientInterface
{
  public function __construct(private HttpClientInterface $client)
  {
    $this->client = $client->withOptions([
      'base_uri' => 'https://nominatim.openstreetmap.org',
    ]);
  }

  public function getLatLngFromAddress(string|int $housenumber, string $street, string $city): string
  {
    try {
      $req = $this->client->request('GET', '/search', [
        'query' => [
          'q' => $housenumber . ' ' . $street . ' ' . $city . ' Nederland',
          'format' => 'json',
        ],
      ]);
    } catch (Exception\TransportExceptionInterface $e) {
      throw new Error('Something went wrong with the request' . $e);
    }

    return $req->getContent();
  }
}
