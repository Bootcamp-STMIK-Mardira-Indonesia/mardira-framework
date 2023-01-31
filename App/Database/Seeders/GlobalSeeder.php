<?php

namespace App\Database\Seeders;

use App\Core\Seeder;

class GlobalSeeder extends Seeder
{
    public function run()
    {
        $this->call(RoleSeeder::class);
    }
}
