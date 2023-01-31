<?php

namespace App\Core\Commands;

use App\Core\Commands\Concerns\MakeCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunGlobalSeederCommand extends Command
{
    use MakeCommand;

    protected string $commandName = 'db:seed';
    protected string $commandDescription = "Run the database seeds";


    protected function configure()
    {
        $this->setName($this->commandName)
            ->setDescription($this->commandDescription);
    }

    // execute seeding database
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        // get global seeder
        $globalSeeder = $this->getGlobalSeeder();
        // run seeding
        $globalSeeder->run();

        // count seeder called

        // looping seeder called
        foreach ($globalSeeder->getSeederCalled() as $seeder) {
            // write info file migration generate succesfully
            $time = round(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"], 2);

            // count dot for info migration
            $dot = 80 - strlen($seeder);

            // text terminal color green and DONE
            $green = "\033[32m" . 'DONE' . "\033[0m";

            // text terminal color blue and name seeder
            $seeder = "\033[34m" . $seeder . "\033[0m";

            // text terminal colour yellow and RUNNING
            $yellow = "\033[33m" . 'RUNNING' . "\033[0m";
            $output->writeln("<info>{$seeder} " . str_repeat('.', $dot) . " {$yellow}</info>");
            $output->writeln("<info>{$seeder} " . str_repeat('.', $dot) . " {$time} ms {$green}</info>");
        }

        // write info file migration generate succesfully
        $output->writeln("<info>Seeding run successfully.</info>");
    }



    // get only global seeder
    protected function getGlobalSeeder()
    {
        // get Only GlobalSeeder
        $globalSeeder = $this->getSeederPath() . '/GlobalSeeder.php';
        // get object namespace
        $globalSeeder = new \App\Database\Seeders\GlobalSeeder();
        return $globalSeeder;
    }

    // get file name seeder
    protected function getFileSeederName($seeder): string
    {
        $seederName = explode('\\', get_class($seeder));
        return end($seederName);
    }

    // get path seeder
    protected function getSeederPath(): string
    {
        return __DIR__ . '/../../Database/Seeders';
    }
}
