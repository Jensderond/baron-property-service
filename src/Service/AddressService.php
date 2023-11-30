<?php

namespace App\Service;

use App\Contract\AddressClientInterface;
use App\Contract\PropertyClientInterface;
use App\Entity\Property;
use App\Model\Address;
use App\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AddressService implements AddressClientInterface
{
    public function __construct(private readonly AddressClientInterface $client)
    {
    }

    /**
     * @return array{
     *   'lat': string,
     *   'lng': string
     * }
     */
    public function getLatLngFromAddress(int $housenumber, string $street, string $city, string $country): array
    {
        $address = $this->client->getLatLngFromAddress($housenumber, $street, $city, $country);
        $data = json_decode($address, true);

        $value = [
            'lat' => '',
            'lng' => '',
        ];

        try {
            $value = [
                'lat' => $data[0]['lat'],
                'lng' => $data[0]['lon'],
            ];

            return $value;
        } catch (\Exception $e) {
            throw new \Exception('Address not found');
            return $value;
        }
    }
}
