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
        $stub = $this->getStub();
        $stub = str_replace('{{version}}', $version, $stub);
        $file = file_get_contents('https://packagist.org/packages/mardira/mardira-framework');
        $version = $this->getVersion($file);
        $this->removeLockFile();
        $this->removeJsonFile();
        $this->initComposer();
        $this->requireCore($version);

        // get files from vendor folder app core
        foreach (glob("vendor/mardira/mardira-framework/App/Core/*") as $file) {

            // get command folder with if statement
            if (basename($file) == 'Commands') {
                // get files from command folder
                foreach (glob("vendor/mardira/mardira-framework/App/Core/Commands/*") as $file) {
                    // if file changed from vendor folder and from App/Core folder show message
                    if (file_exists($file) && file_exists("App/Core/Commands/" . basename($file))) {
                        // check difference between files from vendor folder and App/Core folder with --dif
                        $diff = shell_exec("diff {$file} App/Core/Commands/" . basename($file));
                        if ($diff) {
                            //split file name from path mardira-framework
                            $file = explode('mardira-framework', $file)[1];
                            // text terminal color yellow and name file
                            $textYellow = "\033[33m" . $file . "\033[0m";

                            $output->writeln("<info>File {$textYellow} has been changed.</info>");
                        }
                    }

                    // if file added from vendor folder and not exist in App/Core folder show message
                    else if (file_exists($file) && !file_exists("App/Core/Commands/" . basename($file))) {
                        //split file name from path mardira-framework
                        $file = explode('mardira-framework', $file)[1];
                        // text terminal color green and name file
                        $textGreen = "\033[32m" . $file . "\033[0m";

                        $output->writeln("<info>File {$textGreen} has been added.</info>");
                    }

                    // if file deleted from vendor folder and exist in App/Core folder show message
                    else if (!file_exists($file) && file_exists("App/Core/Commands/" . basename($file))) {
                        $file = implode('/', ['/App/Core/Commands', basename($file)]);
                        // text terminal color red and name file
                        $textRed = "\033[31m" . $file . "\033[0m";

                        $output->writeln("<info>File {$textRed} has been deleted.</info>");
                    }
                }
            }

            // if file changed from vendor folder and from App/Core folder show message
            if (file_exists($file) && file_exists("App/Core/" . basename($file))) {
                // check difference between files from vendor folder and App/Core folder with --dif
                $diff = shell_exec("diff {$file} App/Core/" . basename($file));
                if ($diff) {
                    //split file name from path mardira-framework
                    $file = explode('mardira-framework', $file)[1];
                    // text terminal color yellow and name file
                    $textYellow = "\033[33m" . $file . "\033[0m";

                    $output->writeln("<info>File {$textYellow} has been changed.</info>");
                }
            }

            // if file added from vendor folder and not exist in App/Core folder show message
            else if (file_exists($file) && !file_exists("App/Core/" . basename($file))) {
                //split file name from path mardira-framework
                $file = explode('mardira-framework', $file)[1];
                // text terminal color green and name file
                $textGreen = "\033[32m" . $file . "\033[0m";

                $output->writeln("<info>File {$textGreen} has been added.</info>");
            }

            // if file deleted from vendor folder and exist in App/Core folder show message
            else if (!file_exists($file) && file_exists("App/Core/" . basename($file))) {

                //implode basename($file) with path /App/Core/
                $file = implode('/', ['/App/Core', basename($file)]);
                $textRed = "\033[31m" . $file . "\033[0m";

                $output->writeln("<info>File {$textRed} has been deleted.</info>");
            }
        }
        $this->removeCoreFolder();
        $this->moveCoreFolder();
        $this->moveJsonFile();
        $this->removeVendorFolder();
        $this->composerUpdate();

        $output->writeln("<info>Application updated successfully.</info>");
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


    protected function removeLockFile(): void
    {
        $command = "rm -rf composer.lock";
        exec($command);
    }

    protected function removeJsonFile(): void
    {
        $command = "rm -rf composer.json";
        exec($command);
    }

    protected function initComposer(): void
    {
        $command = "composer init --no-interaction";
        exec($command);
    }

    protected function requireCore($version): void
    {
        $version = explode('v', $version)[1];
        $command = "composer require mardira/mardira-framework:{$version}";
        exec($command);
    }

    protected function removeCoreFolder(): void
    {
        $command = "rm -rf App/Core";
        exec($command);
    }

    protected function moveCoreFolder(): void
    {
        $command = "mv vendor/mardira/mardira-framework/App/Core App";
        exec($command);
    }

    protected function moveJsonFile(): void
    {
        $command = "mv vendor/mardira/mardira-framework/composer.json .";
        exec($command);
    }

    protected function removeVendorFolder(): void
    {
        $command = "rm -rf vendor";
        exec($command);
    }

    protected function composerUpdate(): void
    {
        $command = "composer update";
        exec($command);
    }

    protected function getStub(): string
    {
        return file_get_contents(__DIR__ . '/stubs/update.stub');
    }

    protected function getFileName(): string
    {
        return 'update';
    }
}
