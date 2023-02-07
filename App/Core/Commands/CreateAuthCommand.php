<?php

namespace App\Core\Commands;

use App\Core\Commands\Concerns\MakeCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateAuthCommand extends Command
{
    use MakeCommand;

    protected string $commandName = 'make:auth';
    protected string $commandDescription = "Creates a new auth";

    protected string $commandOptionName = 'refresh';
    protected string $commandOptionDescription = 'Refresh the auth';

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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // if refresh Auth

        if ($input->getOption($this->commandOptionName)) {
            $this->makeAuthPackages();
            $this->makeAuthController();
            $output->writeln("<info>Auth refreshed successfully.</info>");
            return;
        }

        // if packages created
        if ($this->authPackagesAlreadyExist()) {
            $output->writeln("<error>Auth already exists!</error>");
            return;
        }
        // if controller created

        if ($this->authControllerAlreadyExist()) {
            $output->writeln("<error>Auth already exists!</error>");
            return;
        }

        $this->makeAuthPackages();
        $this->makeAuthController();
        $this->makeAuthEnv();
        $output->writeln("<info>Auth created successfully.</info>");
    }

    protected function getStubPackage()
    {
        return file_get_contents(__DIR__ . '/Stubs/Auth/auth-package.stub');
    }

    protected function getFileNamePackges()
    {
        return 'Auth.php';
    }

    protected function getFilePathAuthPackages()
    {
        return __DIR__ . '/../../Packages/Auth.php';
    }

    protected function authPackagesAlreadyExist()
    {
        return file_exists($this->getFilePathAuthPackages());
    }

    protected function getStubController()
    {
        return file_get_contents(__DIR__ . '/Stubs/Auth/auth-controller.stub');
    }

    protected function getFileNameController()
    {
        return 'AuthController.php';
    }

    protected function getFilePathAuthController()
    {
        return __DIR__ . '/../../Controllers/AuthController.php';
    }

    protected function authControllerAlreadyExist()
    {
        return file_exists($this->getFilePathAuthController());
    }

    protected function getStubEnv()
    {
        return file_get_contents(__DIR__ . '/Stubs/Auth/auth-env.stub');
    }

    protected function getFileNameEnv()
    {
        return '.env';
    }

    protected function getFilePathAuthEnv()
    {
        return __DIR__ . '/../../../.env';
    }

    protected function makeAuthPackages()
    {
        $stub = $this->getStubPackage();
        $stub = str_replace(
            ['{{namespace}}'],
            ['App\Packages'],
            $stub
        );
        file_put_contents($this->getFilePathAuthPackages(), $stub);
    }

    protected function makeAuthController()
    {
        $stub = $this->getStubController();
        $stub = str_replace(
            ['{{namespace}}'],
            ['App\Http\Controllers'],
            $stub
        );
        file_put_contents($this->getFilePathAuthController(), $stub);
    }

    protected function makeAuthEnv()
    {
        $stub = $this->getStubEnv();
        file_put_contents($this->getFilePathAuthEnv(), $stub, FILE_APPEND);
    }
}
