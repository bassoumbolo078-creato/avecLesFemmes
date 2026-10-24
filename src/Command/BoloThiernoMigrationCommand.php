<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'bolothierno:migration',
    description: 'Génère la migration puis l\'exécute.',
)]
class BoloThiernoMigrationCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $app = $this->getApplication();

        $io->section('Génération de la migration');
        $makeInput = new ArrayInput([]);
        $makeInput->setInteractive(false);
        $code = $app->find('make:migration')->run($makeInput, $output);
        if (Command::SUCCESS !== $code) {
            $io->error('Échec de la génération de la migration.');

            return Command::FAILURE;
        }

        $io->section('Exécution de la migration');
        $migrateInput = new ArrayInput([]);
        $migrateInput->setInteractive(false);
        $code = $app->find('doctrine:migrations:migrate')->run($migrateInput, $output);
        if (Command::SUCCESS !== $code) {
            $io->error('Échec de l\'exécution de la migration.');

            return Command::FAILURE;
        }

        $io->success('Migration générée et exécutée.');

        return Command::SUCCESS;
    }
}
