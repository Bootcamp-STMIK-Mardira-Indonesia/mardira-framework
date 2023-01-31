<?php

namespace App\Core\Commands;

use App\Core\Commands\Concerns\MakeCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunMigrateCommand extends Command
{
    use MakeCommand;

    protected string $commandName = 'migrate';
    protected string $commandDescription = "Run all migrations";

    protected function configure()
    {
        $this->setName($this->commandName)
            ->setDescription($this->commandDescription);
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
        $migration->up();
    }

    protected function getStub(): string
    {
        if (!file_exists($this->getStubPath())) {
            throw new \Exception('Stub not found');
        }

        return $this->getStubPath();
    }

    protected function getStubPath(): string
    {
        return __DIR__ . '/Stubs/migration.stub';
    }

    protected function getMigrationName(string $name): string
    {
        return date('Y_m_d_His') . '_' . $name . '.php';
    }
}
