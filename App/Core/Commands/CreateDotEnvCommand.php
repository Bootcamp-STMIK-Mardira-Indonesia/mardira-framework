<?php

namespace App\Core\Commands;

use App\Core\Commands\Concerns\MakeCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateDotEnvCommand extends Command
{
    use MakeCommand;

    protected string $commandName = 'make:env';
    protected string $commandDescription = "Creates a new .env file";
    protected function configure()
    {
        $this->setName($this->commandName)
            ->setDescription($this->commandDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        // check if .env exist
        if ($this->alreadyExists()) {
            $output->writeln("<error>.env already exists!</error>");
            return;
        }

        $this->make();
        $output->writeln("<info>.env created successfully.</info>");
    }

    protected function alreadyExists()
    {
        return file_exists($this->getFilePath($this->getFileName('.env')));
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
        return __DIR__ . '\Stubs\env.stub';
    }

    protected function getFileName($name)
    {
        return $name;
    }

    protected function getReplacements()
    {
        // string random 64 characters different from each other and generate
        $accessToken = bin2hex(random_bytes(32));
        $refreshToken = bin2hex(random_bytes(32));
        $replacements = [
            'DummyAccessToken' => $accessToken,
            'DummyRefreshToken' => $refreshToken,
            'DummyLocalhost' => 'localhost',
            'DummyUsername' => 'root',
            'DummyPassword' => '',
            'DummyDbName' => 'mardira',
        ];
        return $replacements;
    }

    protected function getFilePath($name)
    {
        return __DIR__ . '/../../../' . $name;
    }

    protected function make()
    {
        $stub = $this->getStub();
        $replacements = $this->getReplacements();
        $filePath = $this->getFilePath($this->getFileName('.env'));

        $file = file_get_contents($stub);

        foreach ($replacements as $key => $value) {
            $file = str_replace("{{ $key }}", $value, $file);
        }

        file_put_contents($filePath, $file);
    }
}
