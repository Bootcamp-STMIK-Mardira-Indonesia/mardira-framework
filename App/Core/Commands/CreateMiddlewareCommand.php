<?php

namespace App\Core\Commands;

use App\Core\Commands\Concerns\MakeCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class CreateMiddlewareCommand extends Command
{

    use MakeCommand;

    protected string $commandName = 'make:middleware';
    protected string $commandDescription = "Creates a new middleware";

    protected string $commandArgumentName = "name";
    protected string $commandArgumentDescription = "Name of the middleware";

    protected function configure()
    {
        $this->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addArgument(
                $this->commandArgumentName,
                InputOption::VALUE_REQUIRED,
                $this->commandArgumentDescription
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        // check if middleware already exists
        if ($this->alreadyExists($name)) {
            $output->writeln("<error>Middleware already exists!</error>");
            return;
        }

        $this->make($name);
        $output->writeln("<info>Middleware created successfully.</info>");
    }

    protected function alreadyExists($name)
    {
        return file_exists($this->getFilePath($this->getFileName($name)));
    }

    protected function getFileName($name)
    {
        return $name . '.php';
    }

    protected function getNamespace()
    {
        return 'App\Middleware';
    }

    protected function getStub()
    {
        return __DIR__ . '/Stubs/middleware.stub';
    }

    protected function getReplacements($name)
    {
        $replacements = [
            'DummyNamespace' => $this->getNamespace(),
            'CoreMiddleware' => 'App\Core\Middleware',
            'DummyClass' => $name,
            'DummyParentClass' => 'Middleware',
        ];

        return $replacements;
    }

    protected function make($name)
    {
        $stub = file_get_contents($this->getStub());
        $replacements = $this->getReplacements($name);

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
