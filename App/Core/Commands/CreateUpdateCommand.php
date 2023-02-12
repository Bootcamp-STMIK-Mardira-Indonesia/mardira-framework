<?php

namespace App\Core\Commands;

use App\Core\Commands\Concerns\MakeCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class CreateUpdateCommand extends Command
{
    protected string $commandName = 'update';
    protected string $commandDescription = "Update the application";

    protected string $commandOptionName = "v";
    protected string $commandOptionDescription = 'Update to the specified version';


    protected function configure()
    {
        $this->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addOption(
                $this->commandOptionName,
                null,
                InputOption::VALUE_OPTIONAL,
                $this->commandOptionDescription
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $version = $input->getOption('version');

        $this->update($version);

        $output->writeln("<info>Application updated successfully.</info>");
    }

    protected function getStub(): string
    {
        return file_get_contents(__DIR__ . '/stubs/update.stub');
    }

    protected function getFileName(): string
    {
        return 'update';
    }

    protected function update($version): void
    {
        $stub = $this->getStub();
        $stub = str_replace('{{version}}', $version, $stub);
        // get file from packagist
        $file = file_get_contents('https://packagist.org/packages/mardira/mardira-framework');
        // get version from file
        $version = $this->getVersion($file);

        $stub = str_replace('{{version}}', $version, $stub);

        // update core folder with new version
        $this->updateCore($version);
    }

    protected function getVersion($file): string
    {
        $version = '';
        $pattern = '/<span class="version-number">(.*)<\/span>/';
        preg_match($pattern, $file, $matches);
        if (isset($matches[1])) {
            $version = $matches[1];
        }
        return $version;
    }

    protected function updateCore($version): void
    {
        // remove composer.lock file
        $command = "rm -rf composer.lock";
        exec($command);

        // remove composer.json file
        $command = "rm -rf composer.json";
        exec($command);

        // composer init no interaction
        $command = "composer init --no-interaction";
        exec($command);

        // split v and version number
        $version = explode('v', $version)[1];
        $command = "composer require mardira/mardira-framework:{$version}";
        exec($command);

        // remove old core folder
        $command = "rm -rf App/Core";
        exec($command);

        // move new core folder
        $command = "mv vendor/mardira/mardira-framework/App/Core .";

        exec($command);

        // move new composer.json file
        $command = "mv vendor/mardira/mardira-framework/composer.json .";
        exec($command);

        $command = "rm -rf vendor";
        exec($command);

        // run composer composer update
        $command = "composer update";
        exec($command);
    }
}
