<?php

namespace App\Command;

use App\Service\Handler\BogObjectHandlerService;
use App\Service\PropertyService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:import-bog-objects')]
class ImportBogObjectsCommand extends Command
{
    public function __construct(protected PropertyService $propertyService, protected BogObjectHandlerService $bogObjectHandlerService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('This commands imports/updates all the bog objects');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Bog object importer',
            '============',
            '',
        ]);

        $data = $this->propertyService->getBogObjects();

        foreach ($data as $object) {
            $output->writeln('<info>Importing object: '.$object->getTitle().'</info>');
            $this->bogObjectHandlerService->handle($object, $output);
        }

        // $this->bogObjectHandlerService->archiveObjects($output);

        $this->bogObjectHandlerService->persist();

        return Command::SUCCESS;
    }
}
