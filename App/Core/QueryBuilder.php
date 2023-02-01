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

    public static function table(string $table)
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder->table = $table;
        return $queryBuilder;
    }

    public function query(string $query)
    {
        $statement = $this->connection->prepare($query);
        $statement->execute();
        return $this->statement;
    }

    public function select(array $columns = ['*'])
    {
        $columns = implode(', ', $columns);
        $query = "SELECT {$columns} FROM {$this->table}";
        $statement = $this->connection->prepare($query);
        $statement->execute();
        $this->statement = $statement;
        return $this;
    }

    public function from(string $table): QueryBuilder
    {
        $query = "SELECT * FROM {$table}";
        $statement = $this->connection->prepare($query);
        $statement->execute();
        $this->statement = $statement;
        return $this;
    }

    public function where(string $column,  string $value, string $operator = '='): QueryBuilder
    {
        $query = "SELECT * FROM {$this->table} WHERE {$column} {$operator} {$value}";
        $statement = $this->connection->prepare($query);
        $statement->execute();
        $this->statement = $statement;
        return $this;
    }

    public function whereIn(string $column, array $values): QueryBuilder
    {
        $values = implode(', ', $values);
        $query = "SELECT * FROM {$this->table} WHERE {$column} IN ({$values})";
        $statement = $this->connection->prepare($query);
        $statement->execute();
        $this->statement = $statement;
        return $this;
    }

    public function whereNotIn(string $column, array $values): QueryBuilder
    {
        $values = implode(', ', $values);
        $query = "SELECT * FROM {$this->table} WHERE {$column} NOT IN ({$values})";
        $statement = $this->connection->prepare($query);
        $statement->execute();
        $this->statement = $statement;
        return $this;
    }

    public function orderBy(string $column, string $order = 'ASC'): QueryBuilder
    {
        $query = "SELECT * FROM {$this->table} ORDER BY {$column} {$order}";
        $statement = $this->connection->prepare($query);
        $statement->execute();
        $this->statement = $statement;
        return $this;
    }

    public function limit(int $limit)
    {
        $query = "SELECT * FROM {$this->table} LIMIT {$limit}";
        $statement = $this->connection->prepare($query);
        $statement->execute();
        $this->statement = $statement;
        return $this;
    }

    public function get(): array
    {
        return $this->statement->fetchAll(PDO::FETCH_OBJ);
    }

    public function first()
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
            foreach ($data as $key => $value) {
                $query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$values})";
                $statement = $this->connection->prepare($query);
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

    public function join(string $joinTable, string $column, string $joinColumn) : QueryBuilder
    {
        $query = "SELECT * FROM {$this->table} JOIN {$joinTable} ON {$this->table}.{$column} = {$joinTable}.{$joinColumn}";
        $statement = $this->connection->prepare($query);
        $statement->execute();
        $this->statement = $statement;
        return $this;
    }
}
