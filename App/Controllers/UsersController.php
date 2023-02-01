<?php

namespace App\Controllers;

use App\Core\Controller;

class UsersController extends Controller
{
    public function index(): void
    {
        $users = $this->model('Users')->all();
        $this->response(200, $users);
    }

    public function show($id): void
    {
        $user = $this->model('Users')->find($id);
        $this->response(200, $user);
    }
}
