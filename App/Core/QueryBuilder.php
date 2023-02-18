<?php

namespace App\Core;

use PDO;
use App\Core\Database;


class QueryBuilder
{
    public ?object $statement = null;
    public ?object $connection = null;
    protected string $table;
    protected array $joins = [];
    protected array $columns = [];
    protected string $query = '';
    protected array $where = [];
    protected array $order = [];
    protected int $limit = 0;

    public function __construct()
    {
        $this->connection = Database::getConnection();
    }


    public static function table(string $table): QueryBuilder
    {
        $queryBuilder = new QueryBuilder();
        $queryBuilder->table = $table;
        return $queryBuilder;
    }

    public function query(string $query): QueryBuilder
    {
        $this->query = $query;
        return $this;
    }

    public function select(array $columns = ['*']): QueryBuilder
    {
        $this->columns = $columns;
        return $this;
    }

    public function from(string $table): QueryBuilder
    {
        $this->table = $table;
        return $this;
    }

    public function where(string $column, $value, string $operator = '='): QueryBuilder
    {
        $this->where[] = [
            'column' => $column,
            'value' => $value,
            'operator' => $operator,
        ];
        return $this;
    }

    public function orWhere(string $column, $value, string $operator = '='): QueryBuilder
    {
        $this->where[] = [
            'column' => $column,
            'value' => $value,
            'operator' => $operator,
            'or' => true,
        ];
        return $this;
    }

    public function whereIn(string $column, array $values): QueryBuilder
    {
        $this->where[] = [
            'column' => $column,
            'value' => $values,
            'operator' => 'IN',
        ];
        return $this;
    }

    public function whereNotIn(string $column, array $values): QueryBuilder
    {
        $this->where[] = [
            'column' => $column,
            'value' => $values,
            'operator' => 'NOT IN',
        ];
        return $this;
    }

    public function whereNull(string $column): QueryBuilder
    {
        $this->where[] = [
            'column' => $column,
            'value' => null,
            'operator' => 'IS NULL',
        ];
        return $this;
    }

    public function whereNotNull(string $column): QueryBuilder
    {
        $this->where[] = [
            'column' => $column,
            'value' => null,
            'operator' => 'IS NOT NULL',
        ];
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): QueryBuilder
    {
        $this->order[] = [
            'column' => $column,
            'direction' => $direction,
        ];
        return $this;
    }

    public function orderByAsc(string $column): QueryBuilder
    {
        return $this->orderBy($column, 'ASC');
    }

    public function orderByDesc(string $column): QueryBuilder
    {
        return $this->orderBy($column, 'DESC');
    }

    public function sum(string $column, string $alias): QueryBuilder
    {
        $this->columns[] = "SUM({$column}) AS {$alias}";
        return $this;
    }

    public function join(string $table, string $first, string $second, string $type = ''): QueryBuilder
    {
        $this->joins[] = [
            'table' => $table,
            'first' => $first,
            'operator' => '=',
            'second' => $second,
            'type' => $type,
        ];
        return $this;
    }

    public function limit(int $limit): QueryBuilder
    {
        $this->limit = $limit;
        return $this;
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


    public function get(): array
    {
        $query = $this->buildQuery();
        $this->statement = $this->connection->prepare($query);
        $this->bindValues();
        $this->statement->execute();
        return $this->statement->fetchAll(PDO::FETCH_OBJ);
    }

    public function first()
    {
        $query = $this->buildQuery();
        $this->statement = $this->connection->prepare($query);
        $this->bindValues();
        $this->statement->execute();
        return $this->statement->fetch(PDO::FETCH_OBJ);
    }

    public function count(): int
    {
        $query = $this->buildQuery();
        $this->statement = $this->connection->prepare($query);
        $this->bindValues();
        $this->statement->execute();
        return $this->statement->rowCount();
    }


    public function update(array $data): bool
    {

        $query = "UPDATE {$this->table} SET ";
        $query .= implode(', ', array_map(function ($column) {
            return $column . ' = :' . $column;
        }, array_keys($data)));
        // split select query
        $sql = $this->buildQuery();
        $where = substr($sql, strpos($sql, 'WHERE'));
        $query .= ' ' . $where;
        $this->statement = $this->connection->prepare($query);
        foreach ($data as $key => $value) {
            $this->statement->bindValue(':' . $key, $value);
        }
        $this->bindValues();
        return $this->statement->execute();
    }



    public function buildQuery(): string
    {
        $query = "SELECT ";

        if (count($this->columns) > 0) {
            $query .= implode(', ', $this->columns);
        } else {
            $query .= '*';
        }

        $query .= " FROM {$this->table}";

        if (count($this->joins) > 0) {
            foreach ($this->joins as $join) {
                $query .= " {$join['type']} JOIN {$join['table']} ON {$join['first']} {$join['operator']} {$join['second']}";
            }
        }

        if (count($this->where) > 0) {
            $query .= ' WHERE ';
            foreach ($this->where as $key => $where) {
                if (is_array($where['value'])) {
                    $query .= "{$where['column']} {$where['operator']} (";
                    foreach ($where['value'] as $value) {
                        //remove reference column name for binding a.id to id
                        if (strpos($where['column'], '.') !== false) {
                            // change to _
                            $columns = str_replace('.', '_', $where['column']);
                        } else {
                            $columns = $where['column'];
                        }
                        $query .= ":{$columns}, ";
                    }
                    $query = rtrim($query, ', ');
                    $query .= ')';
                } else {
                    //remove reference column name for binding a.id to id
                    if (strpos($where['column'], '.') !== false) {
                        // change to _
                        $columns = str_replace('.', '_', $where['column']);
                    } else {
                        $columns = $where['column'];
                    }

                    $query .= "{$where['column']} {$where['operator']} :{$columns}";
                }
                if ($key < count($this->where) - 1) {
                    // if isset or
                    $query .= ' AND ';
                }
            }
        }

        if (count($this->order) > 0) {
            $query .= ' ORDER BY ';
            foreach ($this->order as $key => $order) {
                $query .= "{$order['column']} {$order['direction']}";
                if ($key < count($this->order) - 1) {
                    $query .= ', ';
                }
            }
        }

        if ($this->limit > 0) {
            $query .= " LIMIT {$this->limit}";
        }

        return $query;
    }

    public function bindValues()
    {

        // fix Invalid bind parameter with alias column

        foreach ($this->where as $where) {
            if (is_array($where['value'])) {
                foreach ($where['value'] as $value) {
                    //remove reference column name for binding a.id to id
                    if (strpos($where['column'], '.') !== false) {
                        // change to _
                        $columns = str_replace('.', '_', $where['column']);
                    } else {
                        $columns = $where['column'];
                    }
                    $this->statement->bindValue(':' . $columns, $value);
                }
            } else {
                //remove reference column name for binding a.id to id
                if (strpos($where['column'], '.') !== false) {
                    // change to _
                    $columns = str_replace('.', '_', $where['column']);
                } else {
                    $columns = $where['column'];
                }
                $this->statement->bindValue(':' . $columns, $where['value']);
            }
        }
    }

    public function delete(string $column, string $value): bool
    {
        $query = "DELETE FROM {$this->table} WHERE {$column} = :value";
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':value', $value);
        return $statement->execute();
    }
}
