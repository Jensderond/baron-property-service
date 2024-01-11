<?php

namespace App\State;

use ApiPlatform\Action\NotFoundAction;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\State\SerializerAwareProviderTrait;
use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use App\Entity\ConstructionNumber;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ConstructionNumberRepository;

class ConstructionNumberProvider implements ProviderInterface
{
    use SerializerAwareProviderTrait;

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, protected SerializerContextBuilderInterface $serializerContextBuilder)
    {
        $this->entityManager = $entityManager;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ConstructionNumber|NotFoundAction
    {
        /** @var ConstructionNumberRepository $constructionNumberRepository */
        $constructionNumberRepository = $this->entityManager->getRepository(ConstructionNumber::class);

        if(!is_numeric($uriVariables['id'])) {
            return new NotFoundAction();
        }

        $number = $constructionNumberRepository->findNonArchivedConstructionNumberById($uriVariables['id']);

        if(!$number) {
            return new NotFoundAction();
        }

        return $number;
    }
}
