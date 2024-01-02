<?php

namespace App\Service\Handler;

use App\Entity\ConstructionType;
use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Service\MediaService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectHandlerService extends AbstractHandlerService
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected MediaService $mediaService
        // , protected FilesystemOperator $publicUploadsStorage, private readonly LoggerInterface $logger
    ) {
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
            $existingProject->setMainImage($this->handleProjectMainImage($existingProject));

            foreach($existingProject->getConstructionTypes() as $constructionType) {
                $constructionType->setMainImage($this->handleConstructionTypeMainImage($constructionType));
            }

            $this->entityManager->persist($existingProject);
            $output->writeln('<info>Updated Project: '.$model->getTitle().'</info>');
            return;
        }

        $model->map($model);
        $model->createSlug();
        $model->setMainImage($this->handleProjectMainImage($model));
        foreach($model->getConstructionTypes() as $constructionType) {
            $constructionType->setMainImage($this->handleConstructionTypeMainImage($constructionType));
        }

        $this->entityManager->persist($model);

        $output->writeln('<info>Added Project: '.$model->getTitle().'</info>');
        return;
    }

    public function persist(): void
    {
        $this->entityManager->flush();
    }

    private function handleConstructionTypeMainImage(ConstructionType $constructionType): array
    {
        $mainImage = current(array_filter($constructionType->getMedia(), function ($media) {
            return $media['soort'] === 'HOOFDFOTO';
        }));

        if (!isset($mainImage)) {
            return [];
        }

        return $this->mediaService->buildObject($mainImage['link']);
    }

    private function handleProjectMainImage(Project $project): array
    {
        $mainImage = current(array_filter($project->getMedia(), function ($media) {
            return $media['soort'] === 'HOOFDFOTO';
        }));

        if (!isset($mainImage) || !$mainImage) {
            return [];
        }

        return $this->mediaService->buildObject($mainImage['link']);
    }
}
