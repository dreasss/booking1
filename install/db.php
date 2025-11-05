<?php
/**
 * @filesource db.php
 *
 * @copyright 2018 Goragod.com
 * @license https://somtum.kotchasan.com/license/
 *
 * @see https://somtum.kotchasan.com/
 */

/*
 * PDO MySql Database Class (CRUD)
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */

class Db
{
    /**
     * @var mixed
     */
    private $connection;
    /**
     * @var mixed
     */
    private $error;

    /**
     * create database connection
     *
     * @param array $db_config
     *
     * @return bool
     */
    public function __construct($db_config)
    {
        $dbdriver = empty($db_config['dbdriver']) ? 'mysql' : $db_config['dbdriver'];
        $hostname = empty($db_config['hostname']) ? 'localhost' : $db_config['hostname'];
        $port = empty($db_config['port']) ? 3306 : $db_config['port'];
        // pdo options
        $options = [
            \PDO::ATTR_PERSISTENT => 1,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ];
        if ($dbdriver == 'mysql') {
            $options[\PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES utf8';
            $options[\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY] = 1;
        }
        // connection string
        $sql = $dbdriver.':host='.$hostname.';port='.$port.';dbname='.$db_config['dbname'];
        // connect to database
        $this->connection = new \PDO($sql, $db_config['username'], $db_config['password'], $options);
        $this->connection->query("SET SESSION sql_mode = ''");
    }

    /**
     * Check if the database schema already exists.
     *
     * @param string $database_name Database name
     *
     * @return bool
     */
    public function databaseExists($database_name)
    {
        $result = $this->customQuery("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$database_name';");
        return empty($result) ? false : true;
    }

    /**
     * Verify that a table exists in the database.
     *
     * @param string $table_name Table name
     *
     * @return bool
     */
    public function tableExists($table_name)
    {
        $result = $this->customQuery("SHOW TABLES LIKE '$table_name'");
        return empty($result) ? false : true;
    }

    /**
     * Check if a column exists on the specified table.
     *
     * @param string $table_name Table name
     * @param string $field      Column name
     *
     * @return bool
     */
    public function fieldExists($table_name, $field)
    {
        $result = $this->customQuery("SHOW COLUMNS FROM $table_name LIKE '$field'");
        return empty($result) ? false : true;
    }

    /**
     * Determine whether a specific index already exists.
     *
     * @param string $table_name Table name
     * @param string $index      Index name
     *
     * @return bool
     */
    public function indexExists($table_name, $index)
    {
        $result = $this->customQuery("SELECT index_name FROM INFORMATION_SCHEMA.STATISTICS WHERE table_name='$table_name' AND index_name='$index'");
        return empty($result) ? false : true;
    }

    /**
     * Check that a column matches the expected data type.
     *
     * @param string $table_name Table name
     * @param string $field      Column name
     * @param string $type       Expected column type prefix
     *
     * @return bool
     */
    public function isColumnType($table_name, $field, $type)
    {
        $result = $this->customQuery("SHOW FIELDS FROM $table_name WHERE `Field`='$field' AND `Type` LIKE '$type%'");
        return empty($result) ? false : true;
    }

    /**
     * Fetch the first row that matches the provided conditions.
     *
     * @param string $table      Table name
     * @param array  $conditions Key-value pairs used in the where clause
     *
     * @return object|bool
     */
    public function first($table, $conditions)
    {
        $result = $this->search($table, $conditions, 1);
        if (is_array($result) && sizeof($result) == 1) {
            return $result[0];
        }
        return false;
    }

    /**
     * Search for rows that satisfy the provided conditions.
     *
     * @param string $table      Table name
     * @param array  $conditions Filter definition
     * @param int    $limit      Maximum rows (0 for all)
     * @param int    $start      Offset for pagination
     * @param string $sort       Sort expression (e.g. id DESC)
     *
     * @return array|bool
     */
    public function search($table, $conditions = [], $limit = 0, $start = 0, $sort = null)
    {
        $keys = [];
        $datas = [];
        $sql = 'SELECT * FROM `'.$table.'`';
        if (!empty($conditions)) {
            foreach ($conditions as $field => $value) {
                if (is_array($value)) {
                    $keys[] = "`$field` IN :$field";
                    $datas[":$field"] = $value;
                } else {
                    $keys[] = "`$field`=:$field";
                    $datas[":$field"] = $value;
                }
            }
            $sql .= ' WHERE '.implode(' AND ', $keys);
        }
        if (!empty($sort)) {
            $sql .= ' ORDER BY '.$sort;
        }
        if ($start > 0 && $limit > 0) {
            $sql .= ' LIMIT '.$start.','.$limit;
        } elseif ($limit > 0) {
            $sql .= ' LIMIT '.$limit;
        }
        try {
            $query = $this->connection->prepare($sql);
            $query->execute($datas);
            $result = [];
            if ($query) {
                while ($row = $query->fetch(\PDO::FETCH_OBJ)) {
                    $result[] = $row;
                }
            }
        } catch (\PDOException $e) {
            $this->error = $e->getMessage();
            $result = false;
        }
        return $result;
    }

    /**
     * Insert a new row into the table.
     *
     * @param string $table Table name
     * @param array  $save  Data to persist (column => value)
     *
     * @return int|bool
     */
    public function insert($table, $save)
    {
        $keys = [];
        $values = [];
        foreach ($save as $key => $value) {
            $keys[] = $key;
            $values[":$key"] = $value;
        }
        $sql = 'INSERT INTO `'.$table.'` (`'.implode('`,`', $keys);
        $sql .= '`) VALUES (:'.implode(',:', $keys).');';
        $query = $this->connection->prepare($sql);
        $query->execute($values);
        return $this->connection->lastInsertId();
    }

    /**
     * Update an existing row using the provided condition.
     *
     * @param string       $table     Table name
     * @param array|int    $condition Primary key or where definition
     * @param array<string,mixed> $save Updated data set
     *
     * @return bool
     */
    public function update($table, $condition, $save)
    {
        $keys = [];
        $values = [];
        foreach ($save as $key => $value) {
            $keys[] = "`$key`=:$key";
            $values[":$key"] = $value;
        }
        $where = $this->createWhere($condition, $values);
        if ($where == '' || sizeof($keys) == 0) {
            return false;
        } else {
            $sql = 'UPDATE `'.$table.'` SET '.implode(',', $keys).' WHERE '.$where.' LIMIT 1';
            $query = $this->connection->prepare($sql);
            $query->execute($values);
            return true;
        }
    }

    /**
     * Delete rows that match the provided condition.
     *
     * @param string    $table     Table name
     * @param array|int $condition Primary key or where definition
     * @param int       $limit     Rows to remove (1 by default)
     *
     * @return bool
     */
    public function delete($table, $condition, $limit = 1)
    {
        $values = [];
        $where = $this->createWhere($condition, $values);
        if ($where == '') {
            return false;
        } else {
            $sql = 'DELETE FROM `'.$table.'` WHERE '.$where;
            if ($limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $query = $this->connection->prepare($sql);
            $query->execute($values);
            return true;
        }
    }

    /**
     * Build the WHERE clause used by update and delete helpers.
     *
     * @param array|int $condition Primary key or where definition
     * @param array     $values    Reference to bound values array
     *
     * @return string
     */
    private function createWhere($condition, &$values)
    {
        if (is_array($condition)) {
            $datas = [];
            foreach ($condition as $key => $value) {
                if (is_array($value)) {
                    $ks = [];
                    $n = 1;
                    foreach ($value as $k => $v) {
                        $_key = ':'.$key.$n;
                        $ks[] = $_key;
                        $values[$_key] = $v;
                        ++$n;
                    }
                    $datas[] = "`$key` IN (".implode(',', $ks).')';
                } else {
                    $datas[] = "`$key`=:$key";
                    $values[":$key"] = $value;
                }
            }
            $where = sizeof($datas) == 0 ? '' : implode(' AND ', $datas);
        } else {
            $id = (int) $condition;
            $where = $id == 0 ? '' : '`id`=:id';
            $values[':id'] = $id;
        }
        return $where;
    }

    /**
     * Execute a statement that does not return a result set.
     *
     * @param string $sql SQL command
     *
     * @return int|bool
     */
    public function query($sql)
    {
        $this->error = '';
        $query = $this->connection->query($sql);
        return $query->rowCount();
    }

    /**
     * Execute a select statement and return the resulting rows.
     *
     * @param string $sql    Query string
     * @param bool   $array  When true rows are returned as arrays
     * @param array  $values Bind values for prepared statements
     *
     * @return array|bool
     */
    public function customQuery($sql, $array = false, $values = null)
    {
        $this->error = '';
        if (empty($values)) {
            $query = $this->connection->query($sql);
        } else {
            $query = $this->connection->prepare($sql);
            $query->execute($values);
        }
        $result = [];
        if ($query) {
            if ($array) {
                while ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
                    $result[] = $row;
                }
            } else {
                while ($row = $query->fetch(\PDO::FETCH_OBJ)) {
                    $result[] = $row;
                }
            }
        }
        return $result;
    }

    /**
     * Get the last database error message.
     *
     * @return string
     */
    public function error()
    {
        return $this->error;
    }
}
