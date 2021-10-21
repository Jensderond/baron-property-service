<?php

namespace App\Service;

use App\Contract\PyberClientInterface;
use App\Entity\Property;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class PropertyService implements PyberClientInterface
{
    private $client;

    public function __construct(PyberClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @return Property[]
     */
    public function getProperties(): array
    {
        $properties = $this->client->getProperties();

        $serializer = new Serializer(
            [new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter()), new GetSetMethodNormalizer(), new ArrayDenormalizer()],
            [new JsonEncoder()]
        );

        return $serializer->deserialize($properties,'App\Entity\Property[]', 'json');
    }
}

