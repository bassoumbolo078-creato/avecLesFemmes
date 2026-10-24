<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'bolothierno:make:entity',
    description: 'Crée ou met à jour une entité (et son repository).',
)]
class BoloThiernoMakeEntityCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::OPTIONAL, 'Nom de l\'entité (ex. Campagne)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
                $arguments = ['command' => 'make:entity'];
        if (null !== $input->getArgument('name')) {
            $arguments['name'] = $input->getArgument('name');
        }

        $makeEntity = $this->getApplication()->find('make:entity');
        $makeInput = new ArrayInput($arguments);
        $makeInput->setInteractive($input->isInteractive());

        return $makeEntity->run($makeInput, $output);
    }
}
