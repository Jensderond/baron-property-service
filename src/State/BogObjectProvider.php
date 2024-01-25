<?php

namespace App\State;

use ApiPlatform\Action\NotFoundAction;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\State\SerializerAwareProviderTrait;
use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use App\Entity\BogObject;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BogObjectRepository;
use Doctrine\Common\Collections\Criteria;

class BogObjectProvider implements ProviderInterface
{
    use SerializerAwareProviderTrait;

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, protected SerializerContextBuilderInterface $serializerContextBuilder)
    {
        $this->entityManager = $entityManager;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|BogObject|NotFoundAction
    {
        switch ($operation->getName()) {
            case 'getExternalBogObject':
                return $this->getItemById($uriVariables['id']);
            case 'getBogObjectCollection':
                return $this->getCollection();
        }
        return new NotFoundAction();
    }

    private function getCollection()
    {
        /** @var BogObjectRepository $bogObjectRepo */
        $bogObjectRepo = $this->entityManager->getRepository(BogObject::class);

        return $bogObjectRepo->findAll();
    }

    private function getItemById(int $id): NotFoundAction|BogObject
    {
        /** @var BogObjectRepository $bogObjectRepo */
        $bogObjectRepo = $this->entityManager->getRepository(BogObject::class);

        $criteria = new Criteria();
        $criteria->where(Criteria::expr()->eq('externalId', $id));
        $criteria->setMaxResults(1);

        $project = $bogObjectRepo->matching($criteria)->first();

        if(!$project) {
            return new NotFoundAction();
        }

        return $project;
    }
}
