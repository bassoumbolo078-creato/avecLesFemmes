<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'bolothierno:migration',
    description: 'Génère la migration puis l\'exécute.',
)]
class BoloThiernoMigrationCommand extends Command
{
    public function __construct(
        #[Autowire('%kernel.project_dir%')] private readonly string $projectDir,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->section('Génération de la migration');
        $makeInput = new ArrayInput(['command' => 'make:migration']);
        $makeInput->setInteractive($input->isInteractive());
        $code = $this->getApplication()->find('make:migration')->run($makeInput, $output);
        if (Command::SUCCESS !== $code) {
            $io->error('Échec de la génération de la migration.');

            return Command::FAILURE;
        }

        // L'exécution se fait dans un nouveau processus : celui-ci relit le dossier
        // migrations/ et voit donc le fichier qui vient d'être généré.
        $io->section('Exécution de la migration');
        $process = new Process(
            [PHP_BINARY, $this->projectDir.'/bin/console', 'doctrine:migrations:migrate', '--no-interaction'],
            $this->projectDir,
            null,
            null,
            120
        );
        $process->run(static function (string $type, string $buffer) use ($output): void {
            $output->write($buffer);
        });
        if (!$process->isSuccessful()) {
            $io->error('Échec de l\'exécution de la migration.');

            return Command::FAILURE;
        }

        $io->success('Migration générée et exécutée.');

        return Command::SUCCESS;
    }
}