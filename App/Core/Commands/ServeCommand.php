<?php

namespace App\Core\Commands;
use App\Core\Commands\Concerns\MakeCommand;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ServeCommand extends Command
{
    use MakeCommand;

    protected string $commandName = 'serve';
    protected string $commandDescription = "Start the PHP development server";

    protected string $commandArgumentName = "host"; // usage: php mardira serve --host=
    protected string $commandArgumentDescription = "The host address to serve the application on";

    protected string $commandOptionName = "port";
    protected string $commandOptionDescription = 'The port to serve the application on';

    protected function configure()
    {
        $this->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addArgument(
                $this->commandArgumentName,
                InputArgument::OPTIONAL,
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
        $host = $input->getArgument('host');
        $port = $input->getOption('port');
        // set default host if empty
        if (empty($host)) {
            $host = '127.0.0.1';
        }

        // set default port if empty
        if (empty($port)) {
            $port = 8000;
        }
        $this->serve($host, $port);

        $output->writeln("<info>Server started successfully.</info>");
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
        return __DIR__ . '/Stubs/serve.stub';
    }

    protected function getTemplate(): string
    {
        if (!file_exists($this->getTemplatePath())) {
            throw new \Exception('Template not found');
        }

        return $this->getTemplatePath();
    }

    protected function getTemplatePath(): string
    {
        return __DIR__ . '/../../Templates/serve.template';
    }

    protected function getConfiguration(): string
    {
        if (!file_exists($this->getConfigurationPath())) {
            throw new \Exception('Configuration not found');
        }

        return $this->getConfigurationPath();
    }

    protected function getConfigurationPath(): string
    {
        return __DIR__ . '/../../Config/serve.yaml';
    }

    protected function getConfigurationKey(): string
    {
        return 'serve';
    }

    public function serve(string $host, string $port): void
    {
        $this->validateHost($host);
        $this->validatePort($port);
        // start host
        $this->startHost($host, $port);
    }

    protected function startHost(string $host, string $port): void
    {
        $this->info("Starting Mardira development server: http://{$host}:{$port}");
        $this->info("Quit the server with CTRL+C.");
        // start server
        $this->startServer($host, $port);
    }

    protected function startServer(string $host, string $port): void
    {
        $this->info("Starting server...");
        // run execute command with PHP built-in server
        $this->executeCommand("php -S {$host}:{$port} -t public");
    }

    protected function executeCommand(string $command): void
    {
        passthru($command);
    }

    protected function info(string $message): void
    {
        echo $message . PHP_EOL;
    }

    protected function validateHost(string $host): void
    {
        $this->validate($host, 'host');
    }

    protected function validatePort(string $port): void
    {
        $this->validate($port, 'port');
    }

    protected function validate(string $value, string $type): void
    {
        $this->validateRequired($value, $type);
        $this->validateType($value, $type);
    }

    protected function validateRequired(string $value, string $type): void
    {
        if (empty($value)) {
            throw new \Exception("The {$type} is required.");
        }
    }

    protected function validateType(string $value, string $type): void
    {
        if ($type === 'host') {
            $this->validateHostType($value);
        }

        if ($type === 'port') {
            $this->validatePortType($value);
        }
    }

    protected function validateHostType(string $host): void
    {
        if (!filter_var($host, FILTER_VALIDATE_IP)) {
            throw new \Exception("The host must be a valid IP address.");
        }
    }

    protected function validatePortType(string $port): void
    {
        if (!is_numeric($port)) {
            throw new \Exception("The port must be a number.");
        }
    }
}
