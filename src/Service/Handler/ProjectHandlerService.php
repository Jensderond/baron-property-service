<?php

namespace App\Service\Handler;

use App\Entity\ConstructionNumber;
use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Repository\ConstructionNumberRepository;
use App\Service\MediaService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectHandlerService extends AbstractHandlerService
{
    /** @var array<int> */
    private array $idsInImport = [];

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected MediaService $mediaService
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
        $existingProject = $projectRepo->findOneBy(['externalId' => $model->getExternalId()], [], 1);
        $existingProjectMediaHash = $existingProject ? $existingProject->getMediaHash() : null;
        $this->idsInImport[] = $model->getExternalId();


        if ($existingProject) {
            /**
             * Array with existing construction numbers and their updated at date
             */
            $constructionNumbers = [];
            foreach ($existingProject->getConstructionTypes() as $constructionType) {
                foreach ($constructionType->getConstructionNumbers() as $constructionNumber) {
                    $constructionNumbers[$constructionNumber->getExternalId()] = $constructionNumber->getMediaHash();
                }
            }
            $existingProject->map($model);
            $existingProject->createSlug();

            if (empty($existingProjectMediaHash) || $existingProjectMediaHash !== $existingProject->getMediaHash()) {
                $output->writeln('<info>Handling media existing project</info>');
                $existingProject->setMainImage($this->handleMainImage($model->getMedia()));
                $existingProject->setMedia($this->mediaService->handleMedia($model->getMedia()));
                $existingProject->setUpdatedAt($model->getUpdatedAt());
            } else {
                $output->writeln('<info>Skipping media existing project</info>');
            }

            foreach ($existingProject->getConstructionTypes() as $constructionType) {
                $constructionType->setMainImage($this->handleMainImage($constructionType->getMedia()));

                foreach ($constructionType->getConstructionNumbers() as $constructionNumber) {
                    $existingMediaHash = $constructionNumbers[$constructionNumber->getExternalId()] ?? null;

                    if ($existingMediaHash !== $constructionNumber->getMediaHash() || $existingMediaHash === null) {
                        $output->writeln('<info>Handling media for: ' . $constructionNumber->getTitle() . '</info>');
                        $constructionNumber->setMedia($this->mediaService->handleMedia($constructionNumber->getMedia()));
                    } else {
                        $output->writeln('<info>Skipping media for: ' . $constructionNumber->getTitle() . '</info>');
                    }
                }
            }

            $this->entityManager->persist($existingProject);
            $output->writeln('<info>Updated Project: ' . $model->getTitle() . '</info>');
            return;
        }

        $model->map($model);
        $model->createSlug();
        $model->setMainImage($this->handleMainImage($model->getMedia()));
        $output->writeln('<info>Handling media for new project</info>');
        $model->setMedia($this->mediaService->handleMedia($model->getMedia()));
        foreach ($model->getConstructionTypes() as $constructionType) {
            $constructionType->setMainImage($this->handleMainImage($constructionType->getMedia()));

            foreach ($constructionType->getConstructionNumbers() as $constructionNumber) {
                $output->writeln('<info>Handling media for new item: ' . $constructionNumber->getTitle() . '</info>');
                $constructionNumber->setMedia($this->mediaService->handleMedia($constructionNumber->getMedia()));
            }
        }

        $this->entityManager->persist($model);

        $output->writeln('<info>Added Project: ' . $model->getTitle() . '</info>');
        return;
    }

    public function persist(): void
    {
        $this->entityManager->flush();
    }

    /**
     * @param OutputInterface $output
     */
    public function archiveItems($output): void
    {
        /** @var ProjectRepository $projectRepo */
        $projectRepo = $this->entityManager->getRepository(Project::class);

        $count = $projectRepo->archiveMissing($this->idsInImport);

        $output->writeln("<info>Archived ${count} projects </info>");
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

        if (!$mainImage || !isset($mainImage['link'])) {
            return [];
        }

        return $this->mediaService->buildObject($mainImage['link'], $options);
    }
}
