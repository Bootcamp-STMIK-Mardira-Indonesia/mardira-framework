<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;

use App\Controllers\UsersController;
use App\Controllers\AuthController;

Router::add('POST', '/login', AuthController::class, 'login');

Router::add('GET', '/users', UsersController::class, 'index');
Router::add('GET', '/users/{id}', UsersController::class, 'show');

Router::run();
