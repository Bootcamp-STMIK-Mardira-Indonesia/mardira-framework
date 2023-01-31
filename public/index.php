<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;

use App\Controllers\UsersController;

Router::add('GET', '/users', UsersController::class, 'index');

Router::run();
