<?php

namespace App\Command;

use App\Service\ProjectManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:brain:generate-project-data',
    description: 'Generates brain.js training data for projects. You have to add the output yourself.',
)]
class BrainGenerateProjectDataCommand extends Command
{
    public function __construct(private ProjectManager $projectManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');
        $dataLengthsQuestion = new Question('How many data points do you want to generate? [50] ', 50);

        $io->title('Generating brain.js training data');

        $dataLengths = intval($helper->ask($input, $output, $dataLengthsQuestion));

        if (0 >= $dataLengths) {
            $io->error('Value entered is not a valid number.');

            return Command::FAILURE;
        }

        $this->projectManager->createJsonTrainingData($dataLengths);

        $io->success(sprintf('Successfully generated %s Brain.js training data in \'data/brainData.json\'.', $dataLengths));

        return Command::SUCCESS;
    }
}
