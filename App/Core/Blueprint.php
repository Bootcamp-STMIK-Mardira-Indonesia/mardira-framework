<?php


namespace App\Core;

use App\Core\Database;
use PDO;

class Blueprint
{
    protected $table;
    protected $columns = [];
    protected $primary = null;
    protected $foreign = null;
    protected $references = null;
    protected $on = null;
    protected $onDelete = null;
    protected $onUpdate = null;
    protected $unique = null;
    protected $index = null;
    protected $default = null;
    protected $nullable = null;
    protected $autoIncrement = null;
    protected $unsigned = null;
    protected $after = null;
    protected $comment = null;
    protected $charset = null;
    protected $collation = null;
    protected $engine = null;
    protected $connection;
    protected $rename = null;
    protected $renameColumn = null;
    protected $newColumn = null;


    public function __construct(string $table)
    {
        $this->table = $table;
        $this->connection = Database::getConnection();
    }

    public function string(string $column, int $length = 255)
    {
        $this->columns[] = $column . ' VARCHAR(' . $length . ')';
        return $this;
    }

    public function integer(string $column, int $length = 11)
    {
        $this->columns[] = $column . ' INT (' . $length . ')';
        return $this;
    }

    public function text(string $column)
    {
        $this->columns[] = $column . ' TEXT';
        return $this;
    }

    public function timestamps()
    {
        $this->columns[] = 'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP';
        $this->columns[] = 'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP';
        return $this;
    }

    public function softDeletes()
    {
        $this->columns[] = 'deleted_at TIMESTAMP NULL';
        return $this;
    }

    public function primary(string $column): Blueprint
    {
        $this->primary = $column;
        return $this;
    }

    public function increment(string $column): Blueprint
    {
        $this->columns[] = $column . ' INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY';
        return $this;
    }

    public function smallIncrement(string $column): Blueprint
    {
        $this->columns[] = $column . ' SMALLINT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY';
        return $this;
    }

    public function tinyIncrement(string $column): Blueprint
    {
        $this->columns[] = $column . ' TINYINT(4) UNSIGNED AUTO_INCREMENT PRIMARY KEY';
        return $this;
    }

    public function foreign(string $column): Blueprint
    {
        $this->foreign = $column;
        return $this;
    }

    public function references(string $column): Blueprint
    {
        $this->references = $column;
        return $this;
    }

    public function on(string $table): Blueprint
    {
        $this->on = $table;
        return $this;
    }

    public function onDelete(string $action): Blueprint
    {
        $this->onDelete = $action;
        return $this;
    }

    public function onUpdate(string $action): Blueprint
    {
        $this->onUpdate = $action;
        return $this;
    }

    public function unique(string $column): Blueprint
    {
        $this->unique = $column;
        return $this;
    }

    public function index(string $column): Blueprint
    {
        $this->index = $column;
        return $this;
    }

    public function default(string $value): Blueprint
    {
        $this->default = $value;
        return $this;
    }

    public function nullable(): Blueprint
    {
        $this->nullable = true;
        return $this;
    }

    public function autoIncrement(): Blueprint
    {
        $this->autoIncrement = true;
        return $this;
    }

    public function unsigned(): Blueprint
    {
        $this->unsigned = true;
        return $this;
    }

    public function after(string $column): Blueprint
    {
        $this->after = $column;
        return $this;
    }

    public function comment(string $comment): Blueprint
    {
        $this->comment = $comment;
        return $this;
    }

    public function charset(string $charset): Blueprint
    {
        $this->charset = $charset;
        return $this;
    }

    public function collation(string $collation): Blueprint
    {
        $this->collation = $collation;
        return $this;
    }

    public function engine(string $engine): Blueprint
    {
        $this->engine = $engine;
        return $this;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    public function setConnection(PDO $connection): void
    {
        $this->connection = $connection;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getPrimary()
    {
        return $this->primary;
    }

    public function getForeign()
    {
        return $this->foreign;
    }

    public function getReferences()
    {
        return $this->references;
    }

    public function getOn()
    {
        return $this->on;
    }

    public function getOnDelete()
    {
        return $this->onDelete;
    }

    public function getOnUpdate()
    {
        return $this->onUpdate;
    }

    public function getUnique()
    {
        return $this->unique;
    }

    public function getIndex()
    {
        return $this->index;
    }

    public function getDefault()
    {
        return $this->default;
    }

    public function getNullable()
    {
        return $this->nullable;
    }

    public function getAutoIncrement()
    {
        return $this->autoIncrement;
    }

    public function getUnsigned()
    {
        return $this->unsigned;
    }

    public function getAfter()
    {
        return $this->after;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function getCharset()
    {
        return $this->charset;
    }

    public function build()
    {
        $sql = 'CREATE TABLE ' . $this->table . ' (';
        $sql .= implode(', ', $this->columns);
        if ($this->primary) {
            $sql .= ', PRIMARY KEY (' . $this->primary . ')';
        }
        if ($this->foreign) {
            $sql .= ', FOREIGN KEY (' . $this->foreign . ') REFERENCES ' . $this->on . '(' . $this->references . ')';
        }
        if ($this->onDelete) {
            $sql .= ' ON DELETE ' . $this->onDelete;
        }
        if ($this->onUpdate) {
            $sql .= ' ON UPDATE ' . $this->onUpdate;
        }
        if ($this->unique) {
            $sql .= ', UNIQUE (' . $this->unique . ')';
        }
        if ($this->index) {
            $sql .= ', INDEX (' . $this->index . ')';
        }
        $sql .= ')';

        if ($this->charset) {
            $sql .= ' CHARSET=' . $this->charset;
        }
        if ($this->collation) {
            $sql .= ' COLLATE=' . $this->collation;
        }
        if ($this->engine) {
            $sql .= ' ENGINE=' . $this->engine;
        }
        if ($this->comment) {
            $sql .= ' COMMENT=' . $this->comment;
        }

        $sql .= ';';

        return $sql;
    }

    public function execute()
    {
        $sql = $this->build();
        // set connection from Database class
        $this->connection->exec($sql);
    }

    public function create()
    {
        $this->execute();
    }

    public function table(string $table): Blueprint
    {
        $this->table = $table;
        return $this;
    }

    public function drop()
    {
        $sql = 'DROP TABLE ' . $this->table . ';';
        $this->connection->exec($sql);
    }

    public function truncate()
    {
        $sql = 'TRUNCATE TABLE ' . $this->table . ';';
        $this->connection->exec($sql);
    }

    public function dropIfExists()
    {
        $sql = 'DROP TABLE IF EXISTS ' . $this->table . ';';
        $this->connection->exec($sql);
    }

    public function truncateIfExists()
    {
        $sql = 'TRUNCATE TABLE IF EXISTS ' . $this->table . ';';
        $this->connection->exec($sql);
    }

    public function rename(string $table): Blueprint
    {
        $this->rename = $table;
        return $this;
    }

    public function renameColumn(string $column, string $newColumn): Blueprint
    {
        $this->renameColumn = $column;
        $this->newColumn = $newColumn;
        return $this;
    }


    public function __toString()
    {
        return $this->build();
    }

    public function __destruct()
    {
        $this->connection = null;
    }

    public function __clone()
    {
        $this->connection = null;
    }
}
