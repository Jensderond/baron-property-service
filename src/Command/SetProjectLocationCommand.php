<?php

namespace App\Command;

use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:set-project-location',
    description: 'Set latitude and longitude for a project',
)]
class SetProjectLocationCommand extends Command
{
    private $projectRepository;
    private $entityManager;

    public function __construct(ProjectRepository $projectRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->projectRepository = $projectRepository;
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        // No need for arguments or options in this command
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        // Fetch projects
        $projects = $this->projectRepository->findAll();
        $projectNames = array_map(function ($project) {
            return $project->getTitle();
        }, $projects);

        // Ask user to select a project
        $projectQuestion = new ChoiceQuestion('Please select a project:', $projectNames);
        $selectedProjectName = $questionHelper->ask($input, $output, $projectQuestion);
        $selectedProject = $this->projectRepository->findOneBy(['title' => $selectedProjectName]);

        // Ask for latitude
        $latQuestion = new Question('Enter latitude:');
        $latQuestion->setValidator(function ($answer) {
            if (!is_numeric($answer) || $answer < -90 || $answer > 90) {
                throw new \RuntimeException('The latitude must be a valid number between -90 and 90.');
            }
            return $answer;
        });
        $lat = $questionHelper->ask($input, $output, $latQuestion);

        // Ask for longitude
        $lngQuestion = new Question('Enter longitude:');
        $lngQuestion->setValidator(function ($answer) {
            if (!is_numeric($answer) || $answer < -180 || $answer > 180) {
                throw new \RuntimeException('The longitude must be a valid number between -180 and 180.');
            }
            return $answer;
        });
        $lng = $questionHelper->ask($input, $output, $lngQuestion);

        // Update project with lat and lng
        $selectedProject->setLat($lat); // Assuming there's a setLat method
        $selectedProject->setLng($lng); // Assuming there's a setLng method
        $this->entityManager->persist($selectedProject);
        $this->entityManager->flush();

        $io->success(sprintf('Project "%s" location updated to lat: %s, lng: %s.', $selectedProjectName, $lat, $lng));

        return Command::SUCCESS;
    }
}
