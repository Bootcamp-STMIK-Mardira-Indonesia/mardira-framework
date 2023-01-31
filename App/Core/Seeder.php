<?php

namespace App\Core;

class Seeder
{
    protected array $seederCalled = [];

    public function call(string $seeder): void
    {
        $seeder = $this->getClassName($seeder);
        $this->addSeederCalled($seeder);
        $seeder = new $seeder();
        $seeder->run();
    }

    protected function getClassName(string $name): string
    {
        return ucfirst($name);
    }

    protected function getSeederPath(): string
    {
        return __DIR__ . '/../Database/Seeders';
    }

    // count seeder called
    public function countSeederCalled(): int
    {
        return count($this->seederCalled);
    }

    // get seeder called
    public function getSeederCalled(): array
    {
        return $this->seederCalled;
    }

    // add seeder called
    public function addSeederCalled(string $seeder): void
    {
        $this->seederCalled[] = $seeder;
    }

    // check seeder called
    public function checkSeederCalled(string $seeder): bool
    {
        return in_array($seeder, $this->seederCalled);
    }
}
