<?php

namespace App\Core\Commands;

use App\Core\Commands\Concerns\MakeCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateSeederCommand extends Command
{
    use MakeCommand;

    protected string $commandName = 'make:seeder';
    protected string $commandDescription = "Creates a new seeder";

    protected string $commandArgumentName = "name";
    protected string $commandArgumentDescription = "Name of the seeder";

    protected function configure()
    {
        $this->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addArgument(
                $this->commandArgumentName,
                InputArgument::REQUIRED,
                $this->commandArgumentDescription
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $name = $input->getArgument('name');
        $this->make($name);

        $output->writeln("<info>Seeder created successfully.</info>");
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
        return __DIR__ . '/Stubs/seeder.stub';
    }

    protected function getDestinationPath(): string
    {
        return __DIR__ . 'App/Database/Migrations';
    }

    protected function getDestinationFileName(string $name): string
    {
        return $this->getClassName($name) . '.php';
    }

    protected function getClassName(string $name): string
    {
        return ucfirst($name);
    }


    protected function getNamespace(): string
    {
        return 'App\Database\Seeders';
    }

    protected function getReplacements($name): array
    {
        return [
            'DummyClassName' => $this->getClassName($name),
            'DummyParentClass' => $this->getParentClassName(),
            'DummyNameSpace' => $this->getNamespace(),
            'DummyCoreSeederNamespace' => $this->getCoreSeederNameSpace(),
        ];
    }

    protected function getParentClassName(): string
    {
        return 'Seeder';
    }

    protected function getCoreSeederNameSpace(): string
    {
        return 'App\Core\Seeder';
    }

    protected function getFileName(string $name): string
    {
        return $name;
    }

    protected function make(string $name): void
    {
        $stub = file_get_contents($this->getStub());

        $replacements = $this->getReplacements($name);

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
