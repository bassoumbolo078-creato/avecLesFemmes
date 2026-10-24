<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'bolothierno:db:create',
    description: 'Supprime la base de données si elle existe, puis la recrée.',
)]
class BoloThiernoDbCreateCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $app = $this->getApplication();

        $io->section('Suppression de la base existante (si elle existe)');
        $code = $app->find('doctrine:database:drop')->run(
            new ArrayInput(['--force' => true, '--if-exists' => true]),
            $output
        );
        if (Command::SUCCESS !== $code) {
            $io->error('Échec de la suppression de la base.');

            return Command::FAILURE;
        }

        $io->section('Création de la base');
        $code = $app->find('doctrine:database:create')->run(new ArrayInput([]), $output);
        if (Command::SUCCESS !== $code) {
            $io->error('Échec de la création de la base.');

            return Command::FAILURE;
        }

        $io->success('Base de données recréée.');

        return Command::SUCCESS;
    }
}
