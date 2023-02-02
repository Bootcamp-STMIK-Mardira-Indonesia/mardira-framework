<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\QueryBuilder as DB;

class UsersController extends Controller
{
    public function index(): void
    {
        $users = DB::table('users')
            ->select(['users.id', 'users.name', 'users.email', 'roles.name as role'])
            ->join('roles', 'roles.id', 'users.role_id', 'left')->get();
        $this->response(200, $users);
    }

    public function show($id): void
    {
        $user = $this->model('Users')->find($id);
        $this->response(200, $user);
    }
}
