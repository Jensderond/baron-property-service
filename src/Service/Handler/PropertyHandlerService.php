<?php

namespace App\Service\Handler;

use App\Entity\Property;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Console\Output\OutputInterface;

class PropertyHandlerService extends AbstractHandlerService
{
    /** @var array<int> */
    private array $idsInImport = [];

    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param Property $model
     * @param OutputInterface $output
     */
    public function handle($model, $output): void
    {
        /** @var PropertyRepository $propertyRepo */
        $propertyRepo = $this->entityManager->getRepository(Property::class);

        /** @var list<Property> $property */
        $property = $propertyRepo->findBy(['externalId' => $model->getExternalId()], [], 1);
        $property = $property[0] ?? null;

        if ($property) {
            $this->idsInImport[] = $property->getExternalId();

            $property->map($model);

            $property->createSlug();

            $this->entityManager->persist($property);
            $output->writeln('<info>Updated Property: '.$model->getTitle().'</info>');
            return;
        }

        $model->createSlug();
        $this->entityManager->persist($model);

        $output->writeln('<info>Added Property: '.$model->getTitle().'</info>');

        return;
    }


    /**
     * @param OutputInterface $output
     */
    public function archiveProperties($output): void
    {
        /** @var PropertyRepository $propertyRepo */
        $propertyRepo = $this->entityManager->getRepository(Property::class);


        $count = $propertyRepo->archiveProperties($this->idsInImport);

        $output->writeln("<info>Archived ${count} properties </info>");
    }

    public function persist(): void
    {
        $this->entityManager->flush();
    }
}
