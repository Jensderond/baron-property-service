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
            $existingProject->setMainImage($this->handleMainImage($existingProject->getMedia()));
            $existingProject->setMedia($this->handleMedia($existingProject->getMedia()));

            foreach($existingProject->getConstructionTypes() as $constructionType) {
                $constructionType->setMainImage($this->handleMainImage($constructionType->getMedia()));

                foreach($constructionType->getConstructionNumbers() as $constructionNumber) {
                    $constructionNumber->setMedia($this->handleMedia($constructionNumber->getMedia()));
                }
            }

            $this->entityManager->persist($existingProject);
            $output->writeln('<info>Updated Project: '.$model->getTitle().'</info>');
            return;
        }

        $model->map($model);
        $model->createSlug();
        $model->setMainImage($this->handleMainImage($model->getMedia()));
        $model->setMedia($this->handleMedia($model->getMedia()));
        foreach($model->getConstructionTypes() as $constructionType) {
            $constructionType->setMainImage($this->handleMainImage($constructionType->getMedia()));

            foreach($constructionType->getConstructionNumbers() as $constructionNumber) {
                $constructionNumber->setMedia($this->handleMedia($constructionNumber->getMedia()));
            }
        }

        $this->entityManager->persist($model);

        $output->writeln('<info>Added Project: '.$model->getTitle().'</info>');
        return;
    }

    public function persist(): void
    {
        $this->entityManager->flush();
    }

    private function handleMainImage(array $mediaItems): array
    {
        $options = [
            'sizes' => [
                '1x' => '400x266',
                '2x' => '800x532',
            ],
        ];

        $mainImage = current(array_filter($mediaItems, function ($media) {
            return $media['soort'] === 'HOOFDFOTO';
        }));

        if (!$mainImage) {
            return [];
        }

        return $this->mediaService->buildObject($mainImage['link'], $options);
    }

    private function handleMedia(?array $mediaInput): array
    {
        if (!isset($mediaInput)) {
            return [];
        }

        $transformedItems = [];

        foreach ($mediaInput as $key => $media) {
            if ($media['soort'] === 'HOOFDFOTO' || $media['soort'] === 'FOTO') {
                $transformedItems[] = $this->mediaService->transfromItem($media);

                unset($mediaInput[$key]);
            }
        }

        $mediaItems = array_merge($transformedItems, array_values($mediaInput));

        return $mediaItems;
    }
}
