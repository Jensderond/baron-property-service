<?php

namespace App\Command;

use App\Service\PropertyService;
use App\Service\Handler\ProjectHandlerService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:import-projects')]
class ImportProjectsCommand extends Command
{
    public function __construct(protected PropertyService $propertyService, protected ProjectHandlerService $projectHandlerService, private readonly LoggerInterface $logger)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('This commands imports/updates all the projects');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Project importer',
            '============',
            '',
        ]);

        $data = $this->propertyService->getProjects();

        foreach ($data as $project) {
            $this->projectHandlerService->handle($project, $output);
        }

        $this->projectHandlerService->persist();

        return Command::SUCCESS;
    }
}
