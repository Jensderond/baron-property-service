<?php

namespace App\State;

use ApiPlatform\Action\NotFoundAction;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\State\SerializerAwareProviderTrait;
use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Project;
use App\Repository\PropertyRepository;

class ProjectProvider implements ProviderInterface
{
    use SerializerAwareProviderTrait;

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, protected SerializerContextBuilderInterface $serializerContextBuilder)
    {
        $this->entityManager = $entityManager;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Project|NotFoundAction
    {
        // dump($this->serializerContextBuilder);
        // dump($operation);
        // set the context to be group 'show_project'
        // $context['groups'] = ['show_project'];

        /** @var ProjectRepository $projectRepo */
        $projectRepo = $this->entityManager->getRepository(Project::class);

        $project = $projectRepo->findOneBy(['externalId' => $uriVariables['id'] ?? null]);

        if(!$project) {
            return new NotFoundAction();
        }

        return $project;
    }
}
