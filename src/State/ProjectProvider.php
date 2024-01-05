<?php

namespace App\State;

use ApiPlatform\Action\NotFoundAction;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\State\SerializerAwareProviderTrait;
use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Project;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\Collection;

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

    private function getItemById(int $id): ?Project
    {
        /** @var ProjectRepository $projectRepo */
        $projectRepo = $this->entityManager->getRepository(Project::class);

        $project = $projectRepo->findOneBy(['externalId' => $id]);

        return $project;
    }
}
