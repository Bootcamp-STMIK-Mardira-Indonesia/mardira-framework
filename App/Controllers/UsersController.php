<?php

namespace App\Controllers;

use App\Core\Controller;

class UsersController extends Controller
{
    public function index() : void
    {
        $users = $this->model('Users')->all();
        $this->response(200, $users);
    }
}
