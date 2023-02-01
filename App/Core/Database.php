<?php

namespace App\Core;

use App\Core\DotEnvKey;

use PDO;
use PDOException;

class Database
{
    public ?object $connection = null;

    public function __construct()
    {
        try {
            $this->connection = new PDO(
                "mysql:host=" . DotEnvKey::get('DB_HOST') . ";dbname=" . DotEnvKey::get('DB_NAME'),
                DotEnvKey::get('DB_USER'),
                DotEnvKey::get('DB_PASS')
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            http_response_code(500);
            echo $e->getMessage();
        }
        return $this->connection;
    }

    public static function getConnection(): object
    {
        return (new self)->connection;
    }
}

$database = new Database();
