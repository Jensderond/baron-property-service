<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArrayInput;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:media-reset',
    description: 'Reset media hash and regenerate media files.'
)]
class MediaResetCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('all', null, InputOption::VALUE_NONE, 'Reset media hash for all entities.')
            ->addOption('entity', null, InputOption::VALUE_REQUIRED, 'Specify the entity to reset media hash for (BogObject, Project, or Property).')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $allOption = $input->getOption('all');
        $entityOption = $input->getOption('entity');

        if ($allOption && $entityOption) {
            $io->error('You cannot specify both --all and --entity options. Please choose one.');
            return Command::FAILURE;
        }

        if ($allOption) {
            $entities = ['BogObject', 'Project', 'Property'];
        } elseif ($entityOption) {
            $entity = ucfirst($entityOption);
            if (!in_array($entity, ['BogObject', 'Project', 'Property'])) {
                $io->error('Invalid entity specified. Please specify one of: BogObject, Project, Property.');
                return Command::FAILURE;
            }
            $entities = [$entity];
        } else {
            $io->error('You must specify either --all or --entity option.');
            return Command::FAILURE;
        }

        foreach ($entities as $entity) {
            $this->resetMediaHash($io, $entity);
        }

        $io->success('Media hash reset and import commands executed successfully.');
        return Command::SUCCESS;
    }

    private function resetMediaHash(SymfonyStyle $io, string $entity): void
    {
        // Reset media hash logic for the given entity
        $io->writeln(sprintf('Resetting media hash for entity: %s', $entity));

        // Reset media hash in the database for the specified entity
        $repository = $this->entityManager->getRepository('App\Entity\\' . $entity);
        $entities = $repository->findAll();
        foreach ($entities as $entityInstance) {
            $entityInstance->setMediaHash("");
            $this->entityManager->persist($entityInstance);
        }
        $this->entityManager->flush();

        // Run the import command for the entity
        $importEntityName = strtolower(preg_replace('/\B([A-Z])/', '-$1', lcfirst($entity)));
        if ($importEntityName === 'property') {
            // Special handling for the entity name "Property"
            $importCommand = 'app:import-properties';
        } else {
            $importCommand = sprintf('app:import-%s', $importEntityName);
        }
        $this->getApplication()->find($importCommand)->run(new ArrayInput([]), $io);
    }
}
