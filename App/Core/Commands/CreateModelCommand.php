<?php

namespace App\Core\Commands;

use App\Core\Commands\Concerns\MakeCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Inflector\InflectorFactory;

class CreateModelCommand extends Command
{
    use MakeCommand;

    protected string $commandName = 'make:model';
    protected string $commandDescription = "Creates a new model";

    protected string $commandArgumentName = "name";
    protected string $commandArgumentDescription = "Name of the model";

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
        $table = $input->getOption('table');

        // if model exist

        if (file_exists($this->getFilePath($this->getFileName($name)))) {
            $infoText = "Model {$name} already exists!";
            $yellowText = "\033[33m" . $infoText . "\033[0m";
            $output->writeln("<info>{$yellowText}</info>");
            return;
        }

        $this->make($name, $table);

        // text terminal green color

        $infoText = "Model created successfully.";
        $greenText = "\033[32m" . $infoText . "\033[0m";

        $output->writeln("<info>{$greenText}</info>");
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
        return __DIR__ . '/Stubs/model.stub';
    }

    protected function getNamespace(): string
    {
        return 'App\Models';
    }

    protected function pluralize(string $name): string
    {
        $inflector = InflectorFactory::create()->build();
        return $inflector->pluralize($name);
    }

    protected function getFileName(string $name): string
    {
        return $name . '.php';
    }

    protected function getReplacements($name, $table): array
    {
        $replacements = [
            'DummyNamespace' => $this->getNamespace(),
            'CoreModel' => 'App\Core\Model',
            'DummyClass' => $name,
            'DummyParentClass' => 'Model',
            'DummyTable' => $table ?? strtolower($this->pluralize($name)),
            'DummyPrimaryKey' => 'id',
        ];
        return $replacements;
    }

    protected function make($name, $model): void
    {
        $stub = file_get_contents($this->getStub());

        $replacements = $this->getReplacements($name, $model);

        $stub = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $stub
        );

        $fileName = $this->getFileName($name);

        $filePath = $this->getFilePath($fileName);

        file_put_contents($filePath, $stub);
    }
}
