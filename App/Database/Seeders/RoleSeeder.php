<?php

namespace App\Database\Seeders;

use App\Core\Seeder;
use App\Core\QueryBuilder as DB;

class RoleSeeder extends Seeder
{
    public function run()
    {
        DB::table('roles')->insert(
            [
                [
                    'name' => 'admin',
                    'description' => 'Administrator'
                ],
                [
                    'name' => 'user',
                    'description' => 'User'
                ]
            ]
        );
    }
}
