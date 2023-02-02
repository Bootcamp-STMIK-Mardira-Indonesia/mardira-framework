<?php

namespace App\Database\Seeders;

use App\Core\Seeder;
use App\Core\QueryBuilder as DB;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'administrator@mail.com',
            'password' => md5('admin'),
            'role_id' => 1,
        ];
        DB::table('users')->insert($data);
    }
}
