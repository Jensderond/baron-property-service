<?php

namespace App\Service;

use App\Contract\PyberClientInterface;
// use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class PyberService implements PyberClientInterface
{
    // private $serializer;
    private $client;

    public function __construct(PyberClientInterface $client)
    {
        // $this->serializer = $serializer;
        $this->client = $client;
    }

    public function getProperties(): array
    {
        $properties = $this->client->getProperties();

        $serializer = new Serializer(
            [new GetSetMethodNormalizer(), new ArrayDenormalizer()],
            [new JsonEncoder()]
        );

        return $serializer->deserialize(json_encode($properties),'App\Entity\Property[]', 'json');//->deserialize($properties, 'App\Entity\Property[]', 'json');
    }
}
