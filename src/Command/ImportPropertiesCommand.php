<?php

namespace App\Command;

use App\Service\Handler\PropertyHandlerService;
use App\Service\PropertyService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportPropertiesCommand extends Command
{
    protected static $defaultName = 'app:import-properties';

    public function __construct(protected PropertyService $propertyService, protected PropertyHandlerService $propertyHandlerService, private readonly LoggerInterface $logger)
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

        $data = $this->propertyService->getProperties();

        foreach ($data as $project) {
            $this->propertyHandlerService->handle($project, $output);
        }

        $this->propertyHandlerService->archiveProperties($output);

        $this->propertyHandlerService->persist();

        return Command::SUCCESS;
    }
}
