<?php

namespace App\Service;

use App\Contract\PropertyClientInterface;
use App\Entity\Property;
use App\Entity\Project;
use App\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class PropertyService implements PropertyClientInterface
{
    public function __construct(private readonly PropertyClientInterface $client, private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @return Property[]
     */
    public function getProperties(): array
    {
        // $properties = $this->client->getProperties();
        $properties = file_get_contents(__DIR__.'/../../fixtures/properties.json');

        $serializer = new Serializer(
            [new PropertyNormalizer($this->entityManager), new ArrayDenormalizer()],
            [new JsonEncoder()]
        );

        $jsonProperties = json_decode($properties, true);

        return $serializer->deserialize(json_encode($jsonProperties['resultaten']), 'App\Entity\Property[]', 'json');
    }

    /**
     * @return Project[]
     */
    public function getProjects(): array
    {
        // $properties = $this->client->getProjects();
        $properties = file_get_contents(__DIR__.'/../../fixtures/project.json');

        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader(new AnnotationReader()));

        $serializer = new Serializer(
            [new ObjectNormalizer($classMetadataFactory, null, null, new PhpDocExtractor()), new ArrayDenormalizer()],
            [new JsonEncoder()]
        );

        $jsonProjects = json_decode($properties, true);

        return $serializer->deserialize(json_encode($jsonProjects['resultaten']), 'App\Entity\Project[]', 'json');
    }
}
