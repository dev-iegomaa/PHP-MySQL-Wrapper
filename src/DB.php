<?php

namespace DevIegomaa\PhpMysqlWrapper;

require_once 'env.php';

class DB
{
    private $connection;
    private $table;
    private $sql;
    private $query;
    public function __construct()
    {
        $this->connection = mysqli_connect(SERVER, USERNAME, PASSWORD, DBNAME, PORT);
    }

    public function table(string $table): DB
    {
        $this->table = "`$table`";
        return $this;
    }

    public function select(array $params): DB
    {
        $columns = "";
        foreach ($params as $table => $param) {
            foreach ($param as $value) {
                if ($value === '*') {
                    $columns .= "`$table`.$value, ";
                } else {
                    $columns .= "`$table`.`$value`, ";
                }
            }
        }
        $columns = rtrim($columns, ', ');
        $this->sql = "SELECT $columns FROM " . $this->table;
        return $this;
    }

    public function innerJoin($table, $foreign, $primary): DB
    {
        $this->sql .= " INNER JOIN `$table` ON `$table`.`$foreign` = " . $this->table . ".`$primary`";
        return $this;
    }

    public function leftJoin($table, $foreign, $primary): DB
    {
        $this->sql .= " LEFT JOIN `$table` ON `$table`.`$foreign` = " . $this->table . ".`$primary`";
        return $this;
    }

    public function rightJoin($table, $foreign, $primary): DB
    {
        $this->sql .= " RIGHT JOIN `$table` ON `$table`.`$foreign` = " . $this->table . ".`$primary`";
        return $this;
    }

    public function where(string $column, string $compare, string|int $value): DB
    {
        $value = (is_int($value)) ? "$value" : "'$value'";
        $this->sql .= " WHERE `$column` $compare $value ";
        return $this;
    }

    public function andWhere(string $column, string $compare, string|int $value): DB
    {
        $value = (is_int($value)) ? "$value" : "'$value'";
        $this->sql .= " && `$column` $compare $value ";
        return $this;
    }

    public function orWhere(string $column, string $compare, string|int $value): DB
    {
        $value = (is_int($value)) ? "$value" : "'$value'";
        $this->sql .= " || `$column` $compare $value ";
        return $this;
    }

    public function betweenAnd(string $column, array $value): DB
    {
        $this->sql .= " WHERE `$column` BETWEEN {$value[0]} AND {$value[1]} ";
        return $this;
    }

    public function limit(int $number): DB
    {
        $this->sql .= " LIMIT $number";
        return $this;
    }

    public function find(int $id)
    {
        return $this->where('id', '=', $id)->first()->query()->getRow();
    }

    public function first(): DB
    {
        $this->sql .= " LIMIT 1";
        return $this;
    }

    public function alter(): DB
    {
        $this->sql = "ALTER TABLE " . $this->table . ' ';
        return $this;
    }

    public function add(string $column, ...$structure): DB
    {
        $sql = '';
        foreach ($structure as $value) {
            $sql .= strtoupper($value) . ' ';
        }
        $this->sql .= " ADD IF NOT EXISTS $column $sql";
        return $this;
    }

    public function order(string $item = null, string $position = null): DB
    {
        $order = (is_null($item)) ? $position : strtoupper($position) . " $item";
        $this->sql .= $order;
        return $this;
    }

    public function dropIndex(string $column): DB
    {
        $this->sql .= " DROP INDEX $column";
        return $this;
    }

    public function dropColumnStructure(string $column): DB
    {
        $this->sql .= " DROP $column";
        return $this;
    }

    public function dropPrimaryKey(): DB
    {
        $this->sql .= " DROP PRIMARY KEY";
        return $this;
    }

    public function modify(string $column, string $datatype = null, array $constraints = null, string $order = null): DB
    {
        $sql = '';
        if ($constraints) {
            foreach ($constraints as $value) {
                $sql .= strtoupper($value) . " ";
            }
        }
        $sql = rtrim($sql, " ");
        $this->sql .= " MODIFY `$column` $datatype $sql $order";
        return $this;
    }

    public function change(string $old, string $new, string $datatype, array $constraints = null, string $order = null): DB
    {
        $sql = '';
        if ($constraints) {
            foreach ($constraints as $value) {
                $sql .= strtoupper($value) . " ";
            }
        }
        $sql = rtrim($sql, " ");
        $this->sql .= " CHANGE `$old` `$new` $datatype $sql $order";
        return $this;
    }

    public function foreignKey(string $column, string $table, string $primary, string $onDelete = 'CASCADE', string $onUpdate = 'CASCADE'): DB
    {
        $this->sql .= " ADD FOREIGN KEY (`$column`) REFERENCES `$table`(`$primary`) ON DELETE $onDelete ON UPDATE $onUpdate";
        return $this;
    }

    public function primaryKey(string $column): DB
    {
        $this->sql .= " ADD PRIMARY KEY (`$column`)";
        return $this;
    }

    public function orderBy(array $columns, string $direction = "ASC"): DB
    {
        $column = "";
        $direction = strtoupper($direction);
        foreach ($columns as $col) {
            $column .= "$col , ";
        }
        $column = rtrim($column, ", ");
        $this->sql .= " ORDER BY $column $direction";
        return $this;
    }

    public function create(string $table, array $schema = []): DB
    {
        $row = "";
        foreach ($schema as $key => $value) {
            $row .= $key . ' ' . strtoupper($value) . ', ';
        }
        $row = rtrim($row, ', ');
        $this->sql .= "CREATE TABLE IF NOT EXISTS `$table`($row)";
        return $this;
    }

    public function drop(array $tables): DB
    {
        $tb = '';
        foreach ($tables as $table) {
            $tb .= "`$table` , ";
        }
        $tb = rtrim($tb, ", ");
        $this->sql .= "DROP TABLE IF EXISTS $tb";
        return $this;
    }

    public function rename(string $to): DB
    {
        $this->sql = "RENAME TABLE " . $this->table . " TO `$to`";
        return $this;
    }

    public function query(): DB
    {
        $sql = $this->sql . ';';
        $this->query = mysqli_query($this->connection, $sql);
        return $this;
    }

    public function getRow(): array
    {
        return mysqli_fetch_assoc($this->query);
    }

    public function getAll(): array
    {
        $data = [];
        while ($row = mysqli_fetch_assoc($this->query)) {
            $data[] = $row;
        }
        return $data;
    }

    private function prepareInsertOrUpdateQuery(array $params): string
    {
        $columns = "";
        foreach ($params as $key => $value) {
            $columns .= (is_int($value)) ? "`$key`=$value," : "`$key`='$value',";
        }
        $columns = rtrim($columns, ',');
        return $columns;
    }

    public function insert(array $params): DB
    {
        $columns = $this->prepareInsertOrUpdateQuery($params);
        $this->sql = "INSERT INTO " . $this->table . " SET $columns";
        return $this;
    }

    public function update(array $params): DB
    {
        $columns = $this->prepareInsertOrUpdateQuery($params);
        $this->sql = "UPDATE " . $this->table . " SET $columns ";
        return $this;
    }

    public function delete(): DB
    {
        $this->sql = "DELETE FROM " . $this->table . ' ';
        return $this;
    }

    public function affectedRow(): int|string
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
