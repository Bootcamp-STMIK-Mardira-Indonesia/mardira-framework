<?php

namespace App\Core\Commands;

use App\Core\Commands\Concerns\MakeCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Core\DotEnvKey;


class RunMigrateDatabaseCommand extends Command
{
    use MakeCommand;

    protected string $commandName = 'migrate:database';
    protected string $commandDescription = "Create database";

    protected function configure()
    {
        $this->setName($this->commandName)
            ->setDescription($this->commandDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $connection = $this->getConnection();

        $sql = "CREATE DATABASE IF NOT EXISTS " . DotEnvKey::get('DB_NAME');

        $connection->exec($sql);

        $output->writeln("<info>Database created successfully.</info>");
    }

    // get connection pdo
    protected function getConnection()
    {
        // pdo run get connect server only unsername and password
        $pdo = new \PDO(
            'mysql:host=' . DotEnvKey::get('DB_HOST'),
            DotEnvKey::get('DB_USER'),
            DotEnvKey::get('DB_PASS')
        );

        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }
}
