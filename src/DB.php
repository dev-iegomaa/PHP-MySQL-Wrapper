<?php
namespace DevIegomaa\PhpMysqlWrapper;
class DB
{
    private $connection;
    private $sql;
    private $query;
    public function __construct(string $server, string $username, string $password, string $database, int $port = 3306)
    {
        $this->connection = mysqli_connect($server, $username, $password, $database, $port);
    }

    public function select(string $table, ...$params): DB
    {
        $columns = "";
        foreach ($params as $param) {
            if ($param === '*') {
                $columns .= "$param,";
            } else {
                $columns .= "`$param`,";
            }
        }
        $columns = rtrim($columns, ',');
        $this->sql = "SELECT $columns FROM `$table` ";
        return $this;
    }

    public function where(string $column, string $compare, string|int $value): DB
    {
        $this->sql .= "WHERE `$column`$compare'$value' ";
        return $this;
    }

    public function andWhere(string $column, string $compare, string|int $value): DB
    {
        $this->sql .= "&& `$column`$compare'$value' ";
        return $this;
    }

    public function orWhere(string $column, string $compare, string|int $value): DB
    {
        $this->sql .= "|| `$column`$compare'$value' ";
        return $this;
    }

    public function betweenAnd(string $column, array $value): DB
    {
        $this->sql .= "WHERE `$column` BETWEEN {$value[0]} AND {$value[1]} ";
        return $this;
    }

    public function query(): DB
    {
        $this->query = mysqli_query($this->connection, $this->sql);
        return $this;
    }

    public function getRow(): array
    {
        return mysqli_fetch_assoc($this->query);
    }

    public function getAll(): array
    {
        $data = [];
        while($row = mysqli_fetch_assoc($this->query))
        {
            $data[] = $row;
        }
        return $data;
    }

    public function insertOrUpdate(string $statement, string $table, array $params): DB
    {
        $statement = strtoupper($statement);
        $columns = "";
        foreach ($params as $key => $value) {
            $columns = (is_int($value)) ? "`$key`=$value," : "`$key`='$value',";
        }
        $columns = rtrim($columns, ',');
        $this->sql = "$statement `$table` SET $columns";
        return $this;
    }

    public function delete(string $table): DB
    {
        $this->sql = "DELETE FROM `$table` ";
        return $this;
    }

    public function affectedRow(): int
    {
        if (mysqli_affected_rows($this->connection) >= 0) {
            return 1;
        }
        return -1;
    }
    public function __destruct()
    {
        mysqli_close($this->connection);
    }
}
