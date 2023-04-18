<?php

namespace App\Core\Commands;

use App\Core\Commands\Concerns\MakeCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Inflector\InflectorFactory;

class CreateRouteCommand extends Command
{
    use  MakeCommand;

    protected string $commandName = 'make:route';
    protected string $commandDescription = "Creates a new route";

    protected string $commandArgumentName = "name";
    protected string $commandArgumentDescription = "Name of the route";

    protected string $commandOptionName = "controller";
    protected string $commandOptionDescription = 'Generate a resource controller for the given model';

    protected function configure()
    {
        $this->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addArgument(
                $this->commandArgumentName,
                InputArgument::OPTIONAL,
                $this->commandArgumentDescription
            )
            ->addOption(
                $this->commandOptionName,
                null,
                InputOption::VALUE_OPTIONAL,
                $this->commandOptionDescription
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $controller = $input->getOption('controller');


        // if controller does not exist from file, ask if user wants to create it with option y/n

        if (!(file_exists('App/Controllers/' . $controller . '.php'))) {
            $infoText = "Controller does not exist. Do you want to create it? (y/n)";
            $yellowText = "\033[33m" . $infoText . "\033[0m";
            $ask = $this->ask($yellowText);
            if ($ask == 'y' || $ask == 'Y') {
                $command = $this->getApplication()->find('make:controller');
                $arguments = [
                    'command' => 'make:controller',
                    'name' => $controller,
                ];
                $input = new \Symfony\Component\Console\Input\ArrayInput($arguments);
                $output = new \Symfony\Component\Console\Output\ConsoleOutput();
                $command->run($input, $output);
            } else {
                $infoText = "Controller not created.";
                $blueText = "\033[34m" . $infoText . "\033[0m";
                $output->writeln($blueText);
                return;
            }
        }
        $this->generateRoute($name, $controller);

        // if method does not exist from controller automatically create it
        $findMethod = $this->findMethod($controller, $name);
        if (!$findMethod) {
            $infoText = "<info>Method {$name} from controller {$controller} does not exist!</info>";
            $yellowText = "\033[33m" . $infoText . "\033[0m";
            $infoText = "Creating method {$name} from controller {$controller}...";
            $blueText = "\033[34m" . $infoText . "\033[0m";
            $infoText = "<info>Method created successfully.</info>";
            $greenText = "\033[32m" . $infoText . "\033[0m";
            $output->writeln($yellowText);
            $output->writeln($blueText);
            $output->writeln($greenText);

            $this->createMethod($controller, $name);
        }

        $infoText = "<info>Route created successfully.</info>";
        $greenText = "\033[32m" . $infoText . "\033[0m";
        $output->writeln($greenText);
    }

    protected function createMethod($controller, $name)
    {
        $controllerPath = $this->getControllerPath($controller);
        $controller = file_get_contents($controllerPath);
        $class = strrpos($controller, '}');
        $controller = substr($controller, 0, $class);
        $controller .= "\tpublic function {$name}()\n\t{\n\t\t\n\t}\n\n}";
        file_put_contents($controllerPath, $controller);
    }

    protected function findMethod($controller, $name)
    {
        $controller = $this->getControllerPath($controller);
        $controller = file_get_contents($controller);
        $controller = explode('public function', $controller);
        foreach ($controller as $key => $value) {
            if (strpos($value, $name) !== false) {
                return true;
            }
        }
        return false;
    }

    protected function ask($question)
    {
        $handle = fopen("php://stdin", "r");
        echo $question . " ";
        $line = fgets($handle);
        return trim($line);
    }


    protected function getUseRouteStub()
    {
        if (!file_exists($this->getUseRouteStubPath())) {
            throw new \Exception('Stub not found');
        }

        return $this->getUseRouteStubPath();
    }

    protected function getUseRouteStubPath()
    {
        return __DIR__ . '/Stubs/routes/use-route.stub';
    }

    protected function getRouteStub()
    {
        if (!file_exists($this->getRouteStubPath())) {
            throw new \Exception('Stub not found');
        }

        return $this->getRouteStubPath();
    }

    protected function getGroupStubMethodPath()
    {
        return __DIR__ . '/Stubs/routes/route-group-method.stub';
    }

    protected function getGroupStubMethod()
    {
        if (!file_exists($this->getGroupStubMethodPath())) {
            throw new \Exception('Stub not found');
        }

        return $this->getGroupStubMethodPath();
    }

    protected function getRouteStubPath()
    {
        return __DIR__ . '/Stubs/routes/route.stub';
    }

    protected function getRoutePath()
    {
        return 'App/Routes/Api.php';
    }

    protected function getReplacements($name, $controller)
    {
        return [
            'DummyRoute' => '/' . $this->splitNameController($controller) . '/' . $this->getAction($name),
            'DummyAction' => $this->getAction($name),
            'DummyController' => $this->getControllerName($controller),
            'DummyNameController' => $this->splitSlashController($controller),
        ];
    }

    protected function splitSlashController($controller)
    {
        $controller = $this->getControllerName($controller);
        // remove controller from last string
        $controller = str_replace('\\', '/', $controller);
        // split if there is /
        if (strpos($controller, '/') !== false) {
            $controller = explode('/', $controller);
            $controller = end($controller);
        }

        return $controller;
    }

    protected function splitNameController($controller)
    {
        $controller = $this->getControllerName($controller);
        // remove controller from last string
        $controller = substr($controller, 0, -10);
        // convert to lowercase
        $controller = strtolower($controller);
        // replace \ with /
        $controller = str_replace('\\', '/', $controller);
        // convert to plural
        $inflector = InflectorFactory::create()->build();
        $controller = $inflector->pluralize($controller);
        return $controller;
    }

    protected function getControllerPath($controller)
    {
        return 'App/Controllers/' . $controller . '.php';
    }

    protected function getAction($name)
    {
        return strtolower($name);
    }

    protected function getControllerName($name)
    {
        $name = str_replace('/', '\\', $name);
        return ucfirst($name);
    }

    protected function generateRoute($name, $controller)
    {
        $replacements = $this->getReplacements($name, $controller);

        $routeStub = $this->getRouteStub();
        $routePath = $this->getRoutePath();

        $useRouteStub = $this->getUseRouteStub();
        $useRouteContent = file_get_contents($useRouteStub);

        $useRouteContent = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $useRouteContent
        );

        $routeStubContent = file_get_contents($routeStub);
        $routeStubContent = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $routeStubContent
        );


        // get file content routes and check last use controller
        $routeContent = file_get_contents($routePath);
        $useControllers = strrpos($routeContent, 'use App\Controllers\\');

        // get last use controller
        $lastUseController = substr($routeContent, $useControllers);
        $lastUseController = substr($lastUseController, 0, strpos($lastUseController, ';') + 1);

        // check if use controller already exists
        if (strpos($routeContent, $useRouteContent) === false) {
            $routeContent = str_replace($lastUseController, $lastUseController . PHP_EOL . $useRouteContent, $routeContent);
        }
        file_put_contents($routePath, $routeContent);

        // get last Router::controller
        $routeContent = file_get_contents($routePath);
        $lastRoute = strrpos($routeContent, 'Router::controller');
        if (!$lastRoute) {
            $lastRoute = substr($lastUseController, 0, strpos($lastUseController, ';') + 1);
        }
        $controllerName = $this->getControllerName($controller);

        $checkRoute = strrpos($routeContent, "Router::controller({$controllerName}::class)");
        if (!$checkRoute) {
            $useControllers = strrpos($routeContent, 'use App\Controllers\\');

            // get last use controller
            $lastUseController = substr($routeContent, $useControllers);
            $lastUseController = substr($lastUseController, 0, strpos($lastUseController, ';') + 1);

            $routeContent = str_replace($lastUseController, $lastUseController . PHP_EOL . PHP_EOL . $routeStubContent, $routeContent);
            file_put_contents($routePath, $routeContent);
        } else {
            $groupStubMethod = $this->getGroupStubMethod();
            $groupStubMethodContent = file_get_contents($groupStubMethod);
            $groupStubMethodContent = str_replace(
                array_keys($replacements),
                array_values($replacements),
                $groupStubMethodContent
            );
            $checkRoute = substr($routeContent, $checkRoute);
            $checkRoute = substr($checkRoute, 0, strpos($checkRoute, '});') + 3);

            // check action in Router::controller
            $checkMethod = strrpos($checkRoute, $this->getAction($name));
            if (!$checkMethod) {
                $lastMethod = strrchr($checkRoute, 'Router::');
                $lastMethod = substr($lastMethod, 0, strpos($lastMethod, ');') + 2);

                if (strpos($lastMethod, str_replace(' ', "\t", $groupStubMethodContent)) === false) {
                    $routeContent = str_replace($lastMethod, $lastMethod . PHP_EOL . "\t" . $groupStubMethodContent, $routeContent);
                    // tab
                    file_put_contents($routePath, $routeContent);
                }
            }
        }
    }
}
