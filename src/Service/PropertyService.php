<?php

namespace App\Service;

use App\Contract\PropertyClientInterface;
use App\Entity\Property;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class PropertyService implements PropertyClientInterface
{
    public function __construct(private readonly PropertyClientInterface $client)
    {
    }

    /**
     * @return Property[]
     */
    public function getProperties(): array
    {
        $properties = $this->client->getProperties();

        $serializer = new Serializer(
            [new DateTimeNormalizer(), new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter(), null, new ReflectionExtractor()), new GetSetMethodNormalizer(), new ArrayDenormalizer()],
            [new JsonEncoder()]
        );

        return $serializer->deserialize($properties, 'App\Entity\Property[]', 'json');
    }
}
