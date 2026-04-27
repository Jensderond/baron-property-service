<?php

namespace App\State;

use ApiPlatform\Symfony\Action\NotFoundAction;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\SerializerContextBuilderInterface;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\State\SerializerAwareProviderTrait;
use App\Entity\Project;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;

class ProjectProvider implements ProviderInterface
{
    use SerializerAwareProviderTrait;

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, protected SerializerContextBuilderInterface $serializerContextBuilder)
    {
        $this->entityManager = $entityManager;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|Project|NotFoundAction
    {
        switch ($operation->getName()) {
            case 'getExternalProjectItem':
                return $this->getItemById($uriVariables['id']);
            case 'getProjectCollection':
                return $this->getCollection();
        }
    }

    private function getCollection()
    {
        /** @var ProjectRepository $projectRepo */
        $projectRepo = $this->entityManager->getRepository(Project::class);

        return $projectRepo->findAll();
    }

    private function getItemById(int $id): NotFoundAction|Project
    {
        /** @var ProjectRepository $projectRepo */
        $projectRepo = $this->entityManager->getRepository(Project::class);

        $criteria = new Criteria();
        $criteria->where(Criteria::expr()->eq('externalId', $id));
        $criteria->setMaxResults(1);

        $project = $projectRepo->matching($criteria)->first();

        if (!$project) {
            return new NotFoundAction();
        }

        return $project;
    }
}
