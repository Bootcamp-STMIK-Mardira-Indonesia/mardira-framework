<?php

namespace App\Core;

use Symfony\Component\Console\Application;

class Kernel
{
    public function __construct()
    {
        $this->init();
    }

    protected function init()
    {
        $this->loadConsole();
    }

    public static function run()
    {
        new static();
    }

    public function loadConsole()
    {
        $console = $this->console();
        $application = new Application();
        foreach ($console as $command) {
            $application->add(new $command());
        }
        $application->run();
    }

    public function console()
    {
        $console = [
            'App\Core\Commands\CreateControllerCommand',
            'App\Core\Commands\CreateModelCommand',
            'App\Core\Commands\CreateMigrationCommand',
            'App\Core\Commands\CreateSeederCommand',
            'App\Core\Commands\CreateDotEnvCommand',
            'App\Core\Commands\CreateAuthCommand',
            'App\Core\Commands\CreateMiddlewareCommand',
            'App\Core\Commands\ServeCommand',
            'App\Core\Commands\RunMigrateCommand',
            'App\Core\Commands\RunMigrateRefreshCommand',
            'App\Core\Commands\RunMigrateDatabaseCommand',
            'App\Core\Commands\RunGlobalSeederCommand',
            'App\Core\Commands\RunSeederCommand',
            'App\Core\Commands\CreateUpdateCommand',
        ];
        return $console;
    }

}
