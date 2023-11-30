<?php

namespace App\Service;

use App\Contract\PropertyClientInterface;
use App\Entity\Property;
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

class PropertyService implements PropertyClientInterface
{
    public function __construct(private readonly PropertyClientInterface $client, private readonly AddressService $addressService)
    {
    }

    /**
     * @return Property[]
     */
    public function getProperties(): array
    {
        $properties = $this->client->getProperties();

        $serializer = new Serializer(
            [new PropertyNormalizer($this->addressService), new ArrayDenormalizer()],
            [new JsonEncoder()]
        );

        $jsonProperties = json_decode($properties, true);

        return $serializer->deserialize(json_encode($jsonProperties['resultaten']), 'App\Entity\Property[]', 'json');
    }
}
