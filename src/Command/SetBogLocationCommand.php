<?php

namespace App\Command;

use App\Repository\BogObjectRepository;
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
    name: 'app:set-bog-location',
    description: 'Set latitude and longitude for a Bog object',
)]
class SetBogLocationCommand extends Command
{
    private $bogObjectRepository;
    private $entityManager;

    public function __construct(BogObjectRepository $bogObjectRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->bogObjectRepository = $bogObjectRepository;
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

        // Fetch objects
        $objects = $this->bogObjectRepository->findAll();
        $objectNames = array_map(function ($object) {
            return $object->getTitle();
            }, $objects);

        // Ask user to select an object
        $objectQuestion = new ChoiceQuestion('Please select a object:', $objectNames);
        $selectedObjectName = $questionHelper->ask($input, $output, $objectQuestion);
        $selectedObject = $this->bogObjectRepository->findOneBy(['title' => $selectedObjectName]);

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

        // Update object with lat and lng
        $selectedObject->setLat($lat); // Assuming there's a setLat method
        $selectedObject->setLng($lng); // Assuming there's a setLng method
        $this->entityManager->persist($selectedObject);
        $this->entityManager->flush();

        $io->success(sprintf('Object "%s" location updated to lat: %s, lng: %s.', $selectedObjectName, $lat, $lng));

        return Command::SUCCESS;
    }
}
