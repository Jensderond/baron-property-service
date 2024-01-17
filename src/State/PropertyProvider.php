<?php

namespace App\State;

use ApiPlatform\Action\NotFoundAction;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\State\SerializerAwareProviderTrait;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Property;
use App\Repository\PropertyRepository;

class PropertyProvider implements ProviderInterface
{
    use SerializerAwareProviderTrait;

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Property|NotFoundAction
    {
        /** @var PropertyRepository $propertyRepo */
        $propertyRepo = $this->entityManager->getRepository(Property::class);

        $property = $propertyRepo->findOneBy(['externalId' => $uriVariables['id'] ?? null]);

        if(!$property) {
            return new NotFoundAction();
        }

        return $property;
    }
}
