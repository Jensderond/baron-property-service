<?php

namespace App\Command;

use App\Entity\Property;
use App\Service\PropertyService;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportPropertiesCommand extends Command
{
    protected static $defaultName = 'app:import-properties';

    public function __construct(protected PropertyService $propertyService, protected FilesystemOperator $publicUploadsFilesystem, protected EntityManagerInterface $entityManager, private LoggerInterface $logger)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('This commands imports/updates all the properties/houses');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Property importer',
            '============',
            '',
        ]);

        $createdProperties = 0;
        $updatedProperties = 0;
        $data = $this->propertyService->getProperties();
        $propertyRepo = $this->entityManager->getRepository(Property::class);

        foreach ($data as $property) {
            /* @var $existingProperty Property */
            if (($existingProperty = $propertyRepo->findOneBy(['id' => $property->getId()])) && $existingProperty->getUpdated()->format('Y-m-d H:i:s') !== $property->getUpdated()->format('Y-m-d H:i:s')) {
                $existingProperty->map($property);
                $existingProperty->setSlug($property->getAddress().'-'.$property->getId());
                $existingProperty->setImage($this->downloadFileIfNotExists($property->getImage()));
                $existingProperty->setImages($this->downloadAllImages($property->getImages()));

                foreach ($property->getPlans() as $plan) {
                    $plan->setUrl($this->downloadFileIfNotExists($plan->getUrl()));
                }

                $this->entityManager->persist($existingProperty);
                ++$updatedProperties;

                continue;
            }

            if (!isset($existingProperty)) {
                $property->setSlug($property->getAddress().'-'.$property->getId());
                $property->setImage($this->downloadFileIfNotExists($property->getImage()));
                $property->setImages($this->downloadAllImages($property->getImages()));

                foreach ($property->getPlans() as $plan) {
                    $plan->setUrl($this->downloadFileIfNotExists($plan->getUrl()));
                }

                $this->entityManager->persist($property);
                ++$createdProperties;
            }
        }

        $this->entityManager->flush();

        $output->write('Saved '.$createdProperties.' properties and updated '.$updatedProperties.' properties');

        return Command::SUCCESS;
    }

    private function downloadFileIfNotExists(?string $url): string|null
    {
        if (!$url && !$this->isValidUrl($url)) {
            return null;
        }
        $relativeUrl = parse_url($url);

        try {
            if ($this->publicUploadsFilesystem->fileExists($relativeUrl['path'])) {
                $this->logger->info('File exists');

                return '/uploads'.$relativeUrl['path'];
            } else {
                $this->logger->info('Downloading file');
                $file = file_get_contents($url);
                $this->publicUploadsFilesystem->write($relativeUrl['path'], $file);

                return '/uploads'.$relativeUrl['path'];
            }
        } catch (FilesystemException $e) {
            $this->logger->error('Filesystem error:'.$e);
        }

        return null;
    }

    private function downloadAllImages(array $images): array
    {
        $tempImages = [];
        foreach ($images as $image) {
            if (!empty($image['url'])) {
                $tempImages[] = $this->downloadFileIfNotExists($image['url']);
            }
        }

        return $tempImages;
    }

    private function isValidUrl(string $url): bool
    {
        $url = parse_url($url);

        return isset($url['scheme']) && in_array($url['scheme'], ['http', 'https'], true);
    }
}
