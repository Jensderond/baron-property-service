<?php

namespace App\Service\Handler;

use App\Entity\ConstructionNumber;
use App\Entity\ConstructionType;
use App\Entity\Project;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectHandlerService extends AbstractHandlerService
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param Project $model
     * @param OutputInterface $output
     */
    public function handle($model, $output): void
    {
        /** @var EntityRepository<Project> $projectRepo */
        $projectRepo = $this->entityManager->getRepository(Project::class);

        /** @var list<Project> $existingProject */
        $existingProject = $projectRepo->findBy(['externalId' => $model->getExternalId()], [], 1);
        $existingProject = $existingProject[0] ?? null;

        if ($existingProject) {
            $existingProject->map($model);

            $existingProject->createSlug();

            $this->entityManager->persist($existingProject);
            $output->writeln('<info>Updated '.$model->getTitle().'</info>');
            return;
        }

        $model->createSlug();
        $this->entityManager->persist($model);

        $output->writeln('<info>Added '.$model->getTitle().'</info>');
        return;
    }

    public function persist(): void
    {
        $this->entityManager->flush();
    }
}
