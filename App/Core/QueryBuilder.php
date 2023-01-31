<?php

namespace App\Core;

use PDO;
use App\Core\Database;


class QueryBuilder
{
    public ?object $statement = null;
    public ?object $connection = null;
    protected string $table;

    public function __construct()
    {
        $this->connection = Database::getConnection();
    }

    public static function table(string $table): object
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder->table = $table;
        return $queryBuilder;
    }

    public function query(string $query): object
    {
        $statement = $this->connection->prepare($query);
        $statement->execute();
        return $statement;
    }

    public function select(string $table, array $columns = ['*']): object
    {
        $columns = implode(', ', $columns);
        $query = "SELECT {$columns} FROM {$table}";
        $statement = $this->connection->prepare($query);
        $statement->execute();
        return $statement;
    }

    public function from(string $table): object
    {
        $query = "SELECT * FROM {$table}";
        $statement = $this->connection->prepare($query);
        $statement->execute();
        return $statement;
    }

    public function where(string $table, string $column, string $operator, string $value): object
    {
        $query = "SELECT * FROM {$table} WHERE {$column} {$operator} :value";
        $statement = $this->connection->prepare($query);
        $statement->bindParam(':value', $value);
        $statement->execute();
        return $statement;
    }

    public function orderBy(string $table, string $column, string $order): object
    {
        $query = "SELECT * FROM {$table} ORDER BY {$column} {$order}";
        $statement = $this->connection->prepare($query);
        $statement->execute();
        return $statement;
    }

    public function limit(string $table, int $limit): object
    {
        $query = "SELECT * FROM {$table} LIMIT {$limit}";
        $statement = $this->connection->prepare($query);
        $statement->execute();
        return $statement;
    }

    public function get(): array
    {
        return $this->statement->fetchAll(PDO::FETCH_OBJ);
    }

    public function first(): object
    {
        return $this->statement->fetch(PDO::FETCH_OBJ);
    }

    public function insert($data): bool
    {
        if (isset($data[0])) {
            $columns = implode(', ', array_keys($data[0]));
            $values = implode(', ', array_map(function ($column) {
                return ':' . $column;
            }, array_keys($data[0])));
            $query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$values})";
            $statement = $this->connection->prepare($query);
            foreach ($data as $key => $value) {
                foreach ($value as $key => $value) {
                    $statement->bindValue(':' . $key, $value);
                }
                $statement->execute();
            }
            return true;
        } else {
            $columns = implode(', ', array_keys($data));
            $values = implode(', ', array_map(function ($column) {
                return ':' . $column;
            }, array_keys($data)));
            $query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$values})";
            $statement = $this->connection->prepare($query);
            foreach ($data as $key => $value) {
                $statement->bindValue(':' . $key, $value);
            }
            return $statement->execute();
        }
    }

    public function update(array $data, string $column, string $value): bool
    {
        // check if multiple rows are to be updated
        if (isset($data[0])) {
            $columns = implode(', ', array_keys($data[0]));
            $values = implode(', ', array_map(function ($column) {
                return ':' . $column;
            }, array_keys($data[0])));
            $query = "UPDATE {$this->table} SET {$columns} WHERE {$column} = :value";
            $statement = $this->connection->prepare($query);
            foreach ($data as $key => $value) {
                foreach ($value as $key => $value) {
                    $statement->bindValue(':' . $key, $value);
                }
                $statement->bindValue(':value', $value);
                $statement->execute();
            }
            return true;
        } else {
            $columns = implode(', ', array_keys($data));
            $values = implode(', ', array_map(function ($column) {
                return ':' . $column;
            }, array_keys($data)));
            $query = "UPDATE {$this->table} SET {$columns} WHERE {$column} = :value";
            $statement = $this->connection->prepare($query);
            foreach ($data as $key => $value) {
                $statement->bindValue(':' . $key, $value);
            }
            $statement->bindValue(':value', $value);
            return $statement->execute();
        }
    }

    public function delete(string $column, string $value): bool
    {
        $query = "DELETE FROM {$this->table} WHERE {$column} = :value";
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':value', $value);
        return $statement->execute();
    }

    public function join(string $joinTable, string $column, string $joinColumn): object
    {
        $query = "SELECT * FROM {$this->table} JOIN {$joinTable} ON {$this->table}.{$column} = {$joinTable}.{$joinColumn}";
        $statement = $this->connection->prepare($query);
        $statement->execute();
        return $statement;
    }
}
