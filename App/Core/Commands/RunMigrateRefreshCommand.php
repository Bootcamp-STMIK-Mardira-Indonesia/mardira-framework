<?php

namespace App\Core\Commands;

use App\Core\Commands\Concerns\MakeCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use App\Core\Commands\RunSeederCommand;

class RunMigrateRefreshCommand extends Command
{
    use MakeCommand;

    protected string $commandName = 'migrate:refresh';
    protected string $commandDescription = "Refresh all migrations";

    // check if -seed typed in terminal or not because value is NULL
    protected string $commandOptionName = "seed"; // usage php migrate:refresh -seed
    protected string $commandOptionDescription = 'The class name of the root seeder';

    protected function configure()
    {
        $this->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addOption(
                $this->commandOptionName,
                null,
                InputOption::VALUE_NONE,
                $this->commandOptionDescription
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {

        $migrations = $this->getMigrations();

        foreach ($migrations as $migration) {
            $this->runMigration($migration);
            // get file name migration
            $migrationName = $this->getFileMigrationName($migration);

            // get info time interval each migration run ms
            $time = round(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"], 2);

            // count dot for info migration
            $dot = 80 - strlen($migrationName);

            // text terminal color green and DONE
            $green = "\033[32m" . 'DONE' . "\033[0m";

            // text terminal color blue and name file migration
            $textBlueMigrationFile = "\033[34m" . $migrationName . "\033[0m";

            $output->writeln("<info>{$textBlueMigrationFile} " . str_repeat('.', $dot) . " {$time} ms {$green}</info>");
        }

        // write info file migration generate succesfully
        $output->writeln("<info>Migrations run successfully.</info>");

        $inputs = $input->getOptions();

        if ($inputs['seed']) {
            $runSeeder = new RunSeederCommand();
            $runSeeder->runGlobalSeeder($output);
        }
    }


    protected function getMigrations(): array
    {
        $migrations = [];

        foreach (glob($this->getMigrationsPath() . '/*.php') as $file) {
            $migrations[] = require_once $file;
        }

        return $migrations;
    }

    protected function getFileMigrationName($migrationName): string
    {
        $migrationName = (new \ReflectionClass($migrationName))->getFileName();
        $migrationName = preg_split('/[\/\\\\]/', $migrationName);
        $migrationName = end($migrationName);
        return $migrationName;
    }

    protected function getMigrationsPath(): string
    {
        return __DIR__ . '/../../Database/Migrations';
    }

    protected function runMigration($migration): void
    {
        $migration->down();
        $migration->up();
    }

    protected function getStub(): string
    {
        if (!file_exists($this->getStubPath())) {
            throw new \Exception('Stub not found');
        }

        return file_get_contents($this->getStubPath());
    }

    protected function getStubPath(): string
    {
        return __DIR__ . '/stubs/migration.stub';
    }
}
