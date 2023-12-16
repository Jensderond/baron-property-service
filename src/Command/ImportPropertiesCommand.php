<?php

namespace App\Command;

use App\Entity\Property;
use App\Service\PropertyService;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportPropertiesCommand extends Command
{
    protected static $defaultName = 'app:import-properties';

    public function __construct(protected PropertyService $propertyService, protected FilesystemOperator $publicUploadsFilesystem, protected EntityManagerInterface $entityManager, private readonly LoggerInterface $logger)
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
        $archivedProperties = 0;
        $idsInImport = [];
        $data = $this->propertyService->getProperties();
        /** @var EntityRepository<Property> $propertyRepo */
        $propertyRepo = $this->entityManager->getRepository(Property::class);

        foreach ($data as $property) {
            $idsInImport[] = $property->getId();
            /** @var list<Property> $existingProperty */
            $existingProperty = $propertyRepo->findBy(['externalId' => $property->getExternalId()], [], 1);
            $existingProperty = $existingProperty[0] ?? null;

            if ($existingProperty) {
                $existingProperty->map($property);
                $existingProperty->setSlug($property->getStreet().'-'.$property->getHouseNumber().$property->getHouseNumberAddition().'-'.$property->getCity().'-'.$property->getExternalId());
                // $existingProperty->setImage($this->downloadFileIfNotExists($property->getImage()));
                // $existingProperty->setImages($this->downloadAllImages($property->getImages()));

                // foreach ($property->getPlans() as $plan) {
                //     $plan->setUrl($this->downloadFileIfNotExists($plan->getUrl()));
                // }

                // $existingProperty->setVideos($property->getVideos());

                $this->entityManager->persist($existingProperty);
                ++$updatedProperties;

                continue;
            }

            // if(isset($existingProperty) && $existingProperty->getArchived() !== $property->getArchived()) {
            //     $existingProperty->setArchived($property->getArchived());
            //     $this->entityManager->persist($existingProperty);
            //     ++$updatedProperties;

            //     continue;
            // }

            if (!isset($existingProperty)) {
                $property->setSlug($property->getStreet().'-'.$property->getHouseNumber().$property->getHouseNumberAddition().'-'.$property->getCity().'-'.$property->getExternalId());

                $this->entityManager->persist($property);
                ++$createdProperties;
            }
        }

        // $disabledProperties = $propertyRepo->createQueryBuilder('p')
        //     ->where('p.id NOT IN (:ids)')
        //     ->andWhere('p.archived = 0 OR p.archived is null')
        //     ->setParameter('ids', $idsInImport, ArrayParameterType::INTEGER)
        //     ->getQuery()
        //     ->getResult();

        // foreach ($disabledProperties as $disabledProperty) {
        //     /* @var $property Property | null */
        //     if ($property = $propertyRepo->findOneBy(['id' => $disabledProperty->getId()])) {
        //         $property->setArchived(true);
        //         $this->entityManager->persist($property);
        //         ++$archivedProperties;
        //     }
        // }

        $this->entityManager->flush();

        $output->write('Created '.$createdProperties.' properties and updated '.$updatedProperties.' properties');
        if ($archivedProperties > 0) {
            $output->writeln([
                '',
                'Archived '.$archivedProperties.' properties',
            ]);
        }

        return Command::SUCCESS;
    }
}
