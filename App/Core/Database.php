<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private const DB_HOST  = 'localhost';
    private const DB_USER = 'root';
    private const DB_PASS = '';
    private const DB_NAME = 'pdo';
    private const DSN = 'mysql:host=' . self::DB_HOST . ';dbname=' . self::DB_NAME . '';
    public ?object $connection = null;

    public function __construct()
    {
        try {
            $this->connection = new PDO(
                self::DSN,
                self::DB_USER,
                self::DB_PASS
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
