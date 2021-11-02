<?php

namespace App\Command;

use App\Entity\Property;
use App\Service\PropertyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportPropertiesCommand extends Command
{
    protected static $defaultName = 'app:import-properties';

    public function __construct(protected PropertyService $propertyService, protected EntityManagerInterface $entityManager)
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
            if (($existingProperty = $propertyRepo->findOneBy(['id' => $property->getId()])) && $existingProperty->getUpdateHash() !== md5(serialize($property))) {
                $property->setUpdateHash(md5(serialize($property)));
                $existingProperty->map($property);
                $existingProperty->setSlug($property->getAddress() . '-' . $property->getId());
                $this->entityManager->persist($existingProperty);
                ++$updatedProperties;

                continue;
            }

            if (!isset($existingProperty)) {
                $property->setUpdateHash(md5(serialize($property)));
                $property->setSlug($property->getAddress() . '-' . $property->getId());
                $this->entityManager->persist($property);
                ++$createdProperties;
            }
        }

        $this->entityManager->flush();

        $output->write('Saved '.$createdProperties.' properties and updated '.$updatedProperties.' properties');

        return Command::SUCCESS;
    }
}
