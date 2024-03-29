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

    protected array $methodList = [
        'get',
        'post',
        'put',
        'patch',
        'delete',
        'options',
    ];

    protected array $commandOptions = [
        'controller' => [
            'name' => 'controller',
            'shortName' => 'c',
            'input' => InputOption::VALUE_OPTIONAL,
            'description' => 'Generate a resource controller for the given model',
        ],
        'parameter' => [
            'name' => 'parameter',
            'shortName' => 'p',
            'input' => InputOption::VALUE_OPTIONAL,
            'description' => 'Parameter of the route',
        ],
        'get' => [
            'name' => 'get',
            'shortName' => null,
            'input' => InputOption::VALUE_NONE,
            'description' => 'Create a new get route',
        ],
        'post' => [
            'name' => 'post',
            'shortName' => null,
            'input' => InputOption::VALUE_NONE,
            'description' => 'Create a new post route',
        ],
        'put' => [
            'name' => 'put',
            'shortName' => null,
            'input' => InputOption::VALUE_NONE,
            'description' => 'Create a new put route',
        ],
        'patch' => [
            'name' => 'patch',
            'shortName' => null,
            'input' => InputOption::VALUE_NONE,
            'description' => 'Create a new patch route',
        ],
        'delete' => [
            'name' => 'delete',
            'shortName' => null,
            'input' => InputOption::VALUE_NONE,
            'description' => 'Create a new delete route',
        ],
        'options' => [
            'name' => 'options',
            'shortName' => null,
            'input' => InputOption::VALUE_NONE,
            'description' => 'Create a new options route',
        ],
    ];

    protected function configure()
    {
        $this->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addArgument(
                $this->commandArgumentName,
                InputArgument::OPTIONAL,
                $this->commandArgumentDescription
            );
        foreach ($this->commandOptions as $option) {
            $this->addOption(
                $option['name'],
                $option['shortName'],
                $option['input'],
                $option['description']
            );
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $controller = $input->getOption('controller');
        // remove any = from controller name
        $controller = str_replace('=', '', $controller);
        $parameter = $input->getOption('parameter');

        // remove any = from parameter name
        $parameter = str_replace('=', '', $parameter);
        // if method is not set, ask user to set it
        $methodName = strpos($name, ':') !== false ? '' : $name;
        if (!$name || strpos($name, ':') !== false) {
            $infoText = "Method is not set. Please type method name: ";
            $yellowText = "\033[33m" . $infoText . "\033[0m";
            $methodName = $this->ask($yellowText);
            if ($methodName == '') {
                $infoText = "Method is not set, route not created.";
                $blueText = "\033[34m" . $infoText . "\033[0m";
                $output->writeln($blueText);
                return;
            }
        }

        $inputs = $input->getOptions();

        $httpVerbs = '';

        foreach ($inputs as $key => $value) {
            if (in_array($key, $this->methodList)) {
                if ($value) {
                    $httpVerbs = $key;
                }
            }
        }

        $httpVerbs = $httpVerbs ? $httpVerbs : 'get';


        // if controller is not set, ask user to set it
        if (!$controller) {
            $infoText = "Controller is not set. Please type controller name: ";
            $yellowText = "\033[33m" . $infoText . "\033[0m";
            $controller = $this->ask($yellowText);
            if ($controller == '') {
                $infoText = "Controller is not set, route not created.";
                $blueText = "\033[34m" . $infoText . "\033[0m";
                $output->writeln($blueText);
                return;
            }
        }

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

        $this->generateRoute($name, $methodName, $controller, $parameter, $httpVerbs);

        //
        $splitRoute = explode('/', $name);
        // filter splitRoute only has parameter
        if (strpos($name, ':') !== false) {
            $splitRoute = array_filter($splitRoute, function ($item) {
                return strpos($item, ':') !== false;
            });
            // remove : and implode with comma
            $parameter = implode(',', str_replace(':', '', $splitRoute));
        }

        // if method does not exist from controller automatically create it
        $findMethod = $this->findMethod($controller, $methodName, $parameter);
        if (!$findMethod) {
            $infoText = "<info>Method {$methodName} from controller {$controller} does not exist!</info>";
            $yellowText = "\033[33m" . $infoText . "\033[0m";
            // if its has parameter, create method with parameter method
            if ($parameter) {
                $infoText = "Creating method {$methodName} with parameter {$parameter} from controller {$controller}...";
                $blueText = "\033[34m" . $infoText . "\033[0m";
            } else {
                $infoText = "Creating method {$methodName} from controller {$controller}...";
                $blueText = "\033[34m" . $infoText . "\033[0m";
            }
            $infoText = "<info>Method created successfully.</info>";
            $greenText = "\033[32m" . $infoText . "\033[0m";
            $output->writeln($yellowText);
            $output->writeln($blueText);
            $output->writeln($greenText);

            $this->createMethod($controller, $methodName, $parameter);
        }

        $infoText = "<info>Route created successfully.</info>";
        $greenText = "\033[32m" . $infoText . "\033[0m";
        $output->writeln($greenText);
    }

    protected function createMethod($controller, $name, $parameter = null)
    {
        $controllerPath = $this->getControllerPath($controller);
        $controller = file_get_contents($controllerPath);
        $class = strrpos($controller, '}');
        $controller = substr($controller, 0, $class);
        // if parameter is not null, create method with parameter
        if ($parameter) {
            // if paramter is separated by comma, explode it
            if (strpos($parameter, ',') !== false) {
                $parameter = explode(',', $parameter);
                $parameter = array_map(function ($item) {
                    return '$' . $item;
                }, $parameter);
                $parameter = implode(', ', $parameter);
            } else {
                $parameter = '$' . $parameter;
            }
            $controller .= "\tpublic function {$name}({$parameter})\n\t{\n\t\t\n\t}\n\n}";
        } else {
            $controller .= "\tpublic function {$name}()\n\t{\n\t\t\n\t}\n\n}";
        }
        file_put_contents($controllerPath, $controller);
    }

    protected function findMethod($controller, $name, $parameter = null)
    {
        $controller = $this->getControllerPath($controller);
        $controller = file_get_contents($controller);
        $controller = explode('public function', $controller);

        foreach ($controller as $key => $value) {
            // if parameter is not null, check if method has parameter
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

    protected function getReplacements($name, $methodName, $controller, $parameter = null, $httpVerbs)
    {
        // if method is index, create route without method
        $actionName = $name === NULL ? $methodName : $name;
        $action = $actionName == 'index' ? '' : '/' . $this->getAction($actionName);
        // change :parameter to {parameter}
        $splitRoute = explode('/', $action);
        // remove only :parameter and add {}
        $splitRoute = array_map(function ($item) {
            return strpos($item, ':') !== false ? '{' . str_replace(':', '', $item) . '}' : $item;
        }, $splitRoute);
        $action = implode('/', $splitRoute);

        $prefix = '/';
        if ($parameter) {
            // if parameter is separated by comma, explode it
            if (strpos($parameter, ',') !== false) {
                $parameter = explode(',', $parameter);
                $parameter = array_map(function ($item) {
                    return '{' . $item . '}';
                }, $parameter);
                $parameter = implode('/', $parameter);
            } else {
                $parameter = '{' . $parameter . '}';
            }
            if (strpos($name, ':') !== false) {
                $prefix = '';
            }

            $dummyRoute = $prefix . $this->splitNameController($controller) . $action . '/' . $parameter;
        } else {
            $dummyRoute = $prefix . $this->splitNameController($controller) . $action;
        }

        return [
            'DummyRoute' => $dummyRoute,
            'DummyAction' => $methodName,
            'DummyController' => $this->getControllerName($controller),
            'DummyNameController' => $this->splitSlashController($controller),
            'DummyHttpVerbs' => $httpVerbs,
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

    protected function generateRoute($name, $methodName, $controller, $parameter = null, $httpVerbs)
    {
        $replacements = $this->getReplacements($name, $methodName, $controller, $parameter, $httpVerbs);
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
        $controllerName = $this->splitSlashController($controller);

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
            $checkMethod = strrpos($checkRoute, $this->getAction($methodName));
            if (!$checkMethod) {
                $lastMethod = strrchr($checkRoute, 'Router::');
                // check if result string end with ->group(function () {
                $checkGroupMethod = substr($checkRoute, -48, strpos($lastMethod, "group(function () {"));
                // if strlength is below to  43 then add string {
                if (strlen($checkGroupMethod) < 43) {
                    $checkGroupMethod = substr($checkRoute, -45, strpos($lastMethod, "group(function () {"));
                }

                // check if method in group controller already exists
                if ($checkGroupMethod == "") {
                    $lastMethod = substr($lastMethod, 0, strpos($lastMethod, ');') + 2);
                    if (strpos($lastMethod, str_replace(' ', "\t", $groupStubMethodContent)) === false) {
                        $routeContent = str_replace($lastMethod, $lastMethod . PHP_EOL . "\t" . $groupStubMethodContent, $routeContent);
                        file_put_contents($routePath, $routeContent);
                    }
                } else {
                    // find stringlast

                    $lastMethod = substr($checkGroupMethod, 0, strpos($checkGroupMethod, '{') + 1);


                    $routeContent = str_replace($lastMethod, $lastMethod . PHP_EOL . "\t" . $groupStubMethodContent, $routeContent);

                    file_put_contents($routePath, $routeContent);
                }
            }
        }
    }
}
