<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;

use App\Controllers\UsersController;
use App\Controllers\AuthController;
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;

Router::add('POST', '/login', AuthController::class, 'login');
Router::add('GET', '/users', UsersController::class, 'index', [
    AuthMiddleware::class,
    AdminMiddleware::class
]);
Router::add('GET', '/users/{id}', UsersController::class, 'show');

Router::run();
