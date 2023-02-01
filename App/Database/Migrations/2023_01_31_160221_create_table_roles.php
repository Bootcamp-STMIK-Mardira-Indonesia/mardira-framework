<?php

namespace App\Database\Migrations;

use App\Core\Migration;
use App\Core\Blueprint;
use App\Core\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increment('id');
            $table->string('name', 50);
            $table->string('description', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('roles');
    }
};
