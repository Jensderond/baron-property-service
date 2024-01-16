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
use Doctrine\Common\Collections\Criteria;

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

        $criteria = new Criteria();
        $criteria->where(Criteria::expr()->eq('externalId', $uriVariables['id']));
        $criteria->setMaxResults(1);

        $number = $constructionNumberRepository->matching($criteria)->first();

        if(!$number) {
            return new NotFoundAction();
        }

        return $number;
    }
}
