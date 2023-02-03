<?php

namespace App\Database\Migrations;

use App\Core\Migration;
use App\Core\Blueprint;
use App\Core\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increment('id');
            $table->string('name', 50);
            $table->string('username', 25);
            $table->string('email', 30);
            $table->string('password', 64);
            $table->integer('role_id');
            $table->integer('token_id');
            $table->index(['role_id','token_id']);
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
