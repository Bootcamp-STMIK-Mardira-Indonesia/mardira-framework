<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Core\QueryBuilder as DB;

class UserController extends Controller
{
    // example with model
    public function index()
    {
        $users = User::all();
        $this->response(200, $users);
    }

    // example with query builder
    public function show($id)
    {
        $user = DB::table('users')->where('id', $id)->first();
        $this->response(200, $user);
    }
}
