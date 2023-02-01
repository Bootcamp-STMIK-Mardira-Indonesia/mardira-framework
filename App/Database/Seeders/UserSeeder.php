<?php

namespace App\Database\Seeders;

use App\Core\Seeder;
use App\Core\QueryBuilder as DB;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Jeffrey Way',
                'username' => 'jeffreyway',
                'email' => 'jeffreyway@gmail.com',
                'password' => password_hash('password', PASSWORD_DEFAULT)
            ],
            [
                'name' => 'Jeffrey Way',
                'username' => 'jeffreyway',
                'email' => 'jeffreyway@gmail.com',
                'password' => password_hash('password', PASSWORD_DEFAULT),
            ]
        ];
        DB::table('users')->insert($data);
    }
}
