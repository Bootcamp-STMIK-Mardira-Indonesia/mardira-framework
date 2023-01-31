<?php

namespace App\Core;

class Migration
{
    protected $connection;

    public function getConnection()
    {
        return $this->connection;
    }
}
