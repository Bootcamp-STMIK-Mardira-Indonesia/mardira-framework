<?php

namespace App\Core\Commands;

use App\Core\Commands\Concerns\MakeCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateControllerCommand extends Command
{
    use MakeCommand;

    protected string $commandName = 'make:controller';
    protected string $commandDescription = "Creates a new controller";

    protected string $commandArgumentName = "name";
    protected string $commandArgumentDescription = "Name of the controller";

    protected string $commandOptionName = "model";
    protected string $commandOptionDescription = 'Generate a resource controller for the given model';

    protected function configure()
    {
        $this->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addArgument(
                $this->commandArgumentName,
                InputArgument::REQUIRED,
                $this->commandArgumentDescription
            )
            ->addOption(
                $this->commandOptionName,
                null,
                InputOption::VALUE_OPTIONAL,
                $this->commandOptionDescription
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $model = $input->getOption('model');

        // check if controller already exists
        if ($this->alreadyExists($name)) {
            $output->writeln("<error>Controller already exists!</error>");
            return;
        }

        $this->make($name, $model);
        $output->writeln("<info>Controller created successfully.</info>");
    }

    protected function alreadyExists($name)
    {
        return file_exists($this->getFilePath($this->getFileName($name)));
    }

    protected function getStub()
    {
        if (!file_exists($this->getStubPath())) {
            throw new \Exception('Stub not found');
        }

        return $this->getStubPath();
    }

    protected function getStubPath()
    {
        return __DIR__ . '/Stubs/controller.stub';
    }

    protected function getNamespace()
    {
        return 'App\Controllers';
    }

    protected function getFileName($name)
    {
        return $name . '.php';
    }

    protected function getReplacements($name, $model)
    {
        $replacements = [
            'DummyNamespace' => $this->getNamespace(),
            'CoreController' => 'App\Core\Controller',
            'DummyClass' => $name,
            'DummyParentClass' => 'Controller',
            'DummyModel' => $model,
        ];
        return $replacements;
    }

    protected function createSubFolder($name)
    {
        $folderName = explode('/', $name);
        $folderName = $folderName;
        $folderPath = '';
        foreach ($folderName as $key => $value) {
            if ($key == count($folderName) - 1) {
                break;
            }

            $folderPath .= $value . '/';

            if (!file_exists($this->getFilePath($folderPath))) {
                mkdir($this->getFilePath($folderPath));
            }
        }
    }

    protected function make($name, $model)
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

        // create sub folder if it doesn't exist
        if (strpos($name, '/') !== false) {
            $this->createSubFolder($name);
            $folderName = explode('/', $name);
            // replace namespace

            $namespace = $this->getNamespace();
            foreach ($folderName as $key => $value) {
                if ($key == count($folderName) - 1) {
                    break;
                }

                $namespace .= '\\' . $value;
            }

            $stub = str_replace(
                $replacements['DummyNamespace'],
                $namespace,
                $stub
            );

            // replace class name
            $className = explode('/', $name);
            $stub = str_replace(
                $replacements['DummyClass'],
                end($className),
                $stub
            );
        }

        file_put_contents($filePath, $stub);
    }
}
