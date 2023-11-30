<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use App\Service\PropertyService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:realworks-test',
    description: 'Add a short description for your command',
)]
class RealworksTestCommand extends Command
{
    public function __construct(protected PropertyService $propertyService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('Test realworks connection');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->propertyService->getProperties();

        return Command::SUCCESS;
    }
}
