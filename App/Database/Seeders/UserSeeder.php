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
            'email' => 'admin@admin.com',
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'role_id' => 1,
        ];
        DB::table('users')->insert($data);
    }
}
