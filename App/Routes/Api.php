<?php

use App\Core\Router;

use App\Controllers\UsersController;
use App\Controllers\AuthController;
use App\Middleware\AuthMiddleware;


Router::controller(UsersController::class,[AuthMiddleware::class])->group(function () {
    Router::get('/users', 'index');
    Router::get('/users/{id}', 'show');
});

Router::controller(AuthController::class)->group(function () {
    Router::post('/auth/login', 'login');
    Router::post('/auth/register', 'register');
});


Router::run();
