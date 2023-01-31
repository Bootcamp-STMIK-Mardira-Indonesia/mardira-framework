<?php

namespace App\Core\Commands;

use App\Core\Commands\Concerns\MakeCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateMigrationCommand extends Command
{
    use MakeCommand;

    protected string $commandName = 'make:migration';
    protected string $commandDescription = "Creates a new migration";

    protected string $commandArgumentName = "name";
    protected string $commandArgumentDescription = "Name of the migration";

    protected string $commandOptionName = "table";
    protected string $commandOptionDescription = 'Generate a resource controller for the given model';

    protected function configure()
    {
        $this->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addArgument(
                $this->commandArgumentName,
                InputArgument::REQUIRED,
                $this->commandArgumentDescription
            )->addOption(
                $this->commandOptionName,
                null,
                InputOption::VALUE_OPTIONAL,
                $this->commandOptionDescription
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $name = $input->getArgument('name');
        // if table null
        $table = $input->getOption('table') ?? $name;

        $this->make($name, $table);

        $output->writeln("<info>Migration created successfully.</info>");
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
        return __DIR__ . '\Stubs\migration.stub';
    }

    protected function getDestinationPath(): string
    {
        return __DIR__ . 'App/Database/Migrations';
    }

    protected function getDestinationFileName(string $name): string
    {
        return date('Y_m_d_His') . '_' . $name . '.php';
    }

    protected function getReplacements(string $name, string $table): array
    {
        return [
            'DummyClass' => $name,
            'DummyNamespace' => $this->getNamespace(),
            'DummyParentClass' => 'Migration',
            'DummyMigrationNamespace' => $this->getMigrationNamespace(),
            'DummyParentClass' => $this->getMigrationClassName(),
            'DummySchemaNameSpace' => $this->getSchemaNamespace(),
            'DummySchemaClassName' => $this->getSchemaClassName(),
            'DummyBlueprintNameSpace' => $this->getBlueprintNamespace(),
            'DummyBlueprintClassName' => $this->getBlueprintClassName(),
            'DummyMigrationCoreNamespace' => $this->getMigrationCoreNamespace(),
            'DummyTable' => $this->getTable($table)
        ];
    }

    protected function getTable(string $table)
    {
        $table = strtolower($table);
        $table = explode('_', $table);
        $table = end($table);
        return $table;
    }

    protected function getMigrationNamespace(): string
    {
        return 'App\Database\Migration';
    }

    protected function getMigrationClassName(): string
    {
        return 'Migration';
    }

    protected function getMigrationCoreNamespace(): string
    {
        return 'App\Core\Migration';
    }

    protected function getSchemaNamespace(): string
    {
        return 'App\Core\Schema';
    }

    protected function getSchemaClassName(): string
    {
        return 'Schema';
    }

    protected function getBlueprintNamespace(): string
    {
        return 'App\Core\Blueprint';
    }

    protected function getBlueprintClassName(): string
    {
        return 'Blueprint';
    }

    protected function getNamespace(): string
    {
        return 'App\Database\Migrations';
    }

    protected function getClassName(string $name): string
    {
        return $name;
    }

    protected function getFileName(string $name): string
    {
        return $name;
    }

    protected function make(string $name, string $table): void
    {
        $stub = file_get_contents($this->getStub());

        $replacements = $this->getReplacements($name, $table);

        $stub = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $stub
        );

        $fileName = $this->getDestinationFileName($name);

        $filePath = $this->getFilePath($fileName);

        file_put_contents($filePath, $stub);
    }
}
