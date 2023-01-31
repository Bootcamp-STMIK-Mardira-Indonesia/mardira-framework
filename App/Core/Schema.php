<?php

namespace App\Core;

use App\Core\Blueprint;

class Schema
{
    public static function create(string $table, callable $callback)
    {
        $blueprint = new Blueprint($table);
        $callback($blueprint);
        $blueprint->create();
    }

    public static function drop(string $table)
    {
        $blueprint = new Blueprint($table);
        $blueprint->drop();
    }

    public static function dropIfExists(string $table)
    {
        $blueprint = new Blueprint($table);
        $blueprint->dropIfExists();
    }

    public static function table(string $table, callable $callback)
    {
        $blueprint = new Blueprint($table);
        $callback($blueprint);
        $blueprint->execute();
    }

    public static function rename(string $table, string $newName)
    {
        $blueprint = new Blueprint($table);
        $blueprint->rename($newName);
    }
}
