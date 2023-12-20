<?php

namespace App\Service\Handler;

use App\Entity\ConstructionNumber;
use App\Entity\ConstructionType;
use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Repository\ConstructionTypeRepository;
use App\Repository\ConstructionNumberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
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
        /** @var ProjectRepository $projectRepo */
        $projectRepo = $this->entityManager->getRepository(Project::class);

        /** @var list<Project> $existingProject */
        $existingProject = $projectRepo->findBy(['externalId' => $model->getExternalId()], [], 1);
        $existingProject = $existingProject[0] ?? null;

        if ($existingProject) {
            $existingProject->map($model);
            $existingProject->createSlug();

            $this->entityManager->persist($existingProject);
            $output->writeln('<info>Updated Project: '.$model->getTitle().'</info>');
            return;
        }

        $model->createSlug();
        $this->entityManager->persist($model);

        $output->writeln('<info>Added Project: '.$model->getTitle().'</info>');
        return;
    }

    public function updateSlug(&$model, $output): void
    {
        $name = (new ReflectionClass($model))->getShortName();
        $model->createSlug();
        $output->writeln('<info>Updated ' . $name . ': ' .$model->getTitle().'</info>');
        return;
    }

    public function persist(): void
    {
        $this->entityManager->flush();
    }
}
