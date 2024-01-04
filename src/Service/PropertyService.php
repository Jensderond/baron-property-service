<?php

namespace App\Service;

use App\Contract\PropertyClientInterface;
use App\Entity\Property;
use App\Entity\Project;
use App\Serializer\Normalizer\ProjectNormalizer;
use App\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
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
        if ($_ENV['APP_ENV'] === 'dev') {
            $properties = file_get_contents(__DIR__.'/../../fixtures/properties.json');
        } else {
            $properties = $this->client->getProperties();
        }

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
        if ($_ENV['APP_ENV'] === 'dev') {
            $properties = file_get_contents(__DIR__.'/../../fixtures/project.json');
        } else {
            $properties = $this->client->getProjects();
        }

        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());

        $extractor = new PropertyInfoExtractor([], [new PhpDocExtractor(), new ReflectionExtractor()]);
        $normalizer = new ObjectNormalizer($classMetadataFactory, new MetadataAwareNameConverter($classMetadataFactory), null, $extractor);

        $projectNormalizer = new ProjectNormalizer($normalizer);

        $serializer = new Serializer(
            [$projectNormalizer, $normalizer, new ArrayDenormalizer()],
            [new JsonEncoder()]
        );

        $jsonProjects = json_decode($properties, true);

        return $serializer->deserialize(json_encode($jsonProjects['resultaten']), 'App\Entity\Project[]', 'json');
    }
}
