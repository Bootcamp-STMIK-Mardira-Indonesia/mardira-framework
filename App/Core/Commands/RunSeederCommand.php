<?php

namespace App\Core\Commands;

use App\Core\Commands\Concerns\MakeCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;


class RunSeederCommand extends Command
{
    use MakeCommand;

    protected string $commandName = 'db:seed';
    protected string $commandDescription = "Run the database seeds";

    protected string $commandOptionName = "class";
    protected string $commandOptionDescription = 'The class name of the root seeder';

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

    // execute seeding database
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        // get class name seeder
        $class = $input->getOption('class');

        // check class name seeder is null
        if (is_null($class)) {
            // run global seeder
            $this->runGlobalSeeder($output);
        } else {
            // run seeder
            $this->runSeeder($class, $output);
        }
    }


    // run GlobalSeeder
    public function runGlobalSeeder(OutputInterface $output): void
    {
        // get global seeder
        $globalSeeder = $this->getGlobalSeeder();
        // run global seeder
        $globalSeeder->run();
        // count seeder called
        $count = count($globalSeeder->getSeederCalled());

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

        // write info seeder generate succesfully
        $output->writeln("<info>Seeded: {$count} seeders</info>");
    }
    // run seeder

    protected function runSeeder(string $class, OutputInterface $output): void
    {
        // get seeder path
        $seederPath = $this->getSeederPath();
        // get seeder file
        $seederFile = $seederPath . '/' . $class . '.php';
        // get namespace seeder
        $class = $this->getNamespaceSeeder($class);

        // check seeder file is exists
        if (!file_exists($seederFile)) {
            // write info file migration generate succesfully
            $output->writeln("<error>Seeder {$class} not found.</error>");
            exit;
        }
        $time = round(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"], 2);

        // count dot for info migration
        $dot = 80 - strlen($class);

        // text terminal color green and DONE
        $green = "\033[32m" . 'DONE' . "\033[0m";

        // text terminal color blue and name seeder

        $class = "\033[34m" . $class . "\033[0m";

        // text terminal colour yellow and RUNNING

        $yellow = "\033[33m" . 'RUNNING' . "\033[0m";

        $output->writeln("<info>{$class} " . str_repeat('.', $dot) . " {$yellow}</info>");

        $output->writeln("<info>{$class} " . str_repeat('.', $dot) . " {$time} ms {$green}</info>");

        // write info seeder generate succesfully
        $output->writeln("<info>Seeded: 1 seeder</info>");
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

    // get namespace seeder
    protected function getNamespaceSeeder($class): string
    {
        return 'App\\Database\\Seeders\\' . $class;
    }

    // get path seeder
    protected function getSeederPath(): string
    {
        return __DIR__ . '/../../Database/Seeders';
    }
}
