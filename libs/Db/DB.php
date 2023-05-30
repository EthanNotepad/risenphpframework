<?php

namespace libs\Db;

use Exception;
use libs\Core\Config;
use PDO;
use PDOException;

class DB implements DbInterface
{
    protected static $dbConfig;

    // Private a static variable to determine whether to instantiate
    private static $db_instance;

    private $table = '';
    private $where = ' WHERE (1 = 1)';
    private $limit = '';
    private $order = '';
    private $field = ' * ';
    private $join = [];

    private function __clone()
    {
        // Private a clone method to prevent cloning outside the object
    }

    private $db;

    protected function config()
    {
        return sprintf("mysql:host=%s;dbname=%s;chartset=%s;port=%s", self::$dbConfig['host'], self::$dbConfig['dbname'], self::$dbConfig['dbcharset'], self::$dbConfig['port']);
    }

    private function __construct()
    {
        try {
            $this->db = new PDO($this->config(), self::$dbConfig['username'], self::$dbConfig['password']);
            // get only associative array
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception("Exception: " . $e->getMessage());
        }
    }

    public static function loadConfig($connect = '', $dbname = '')
    {
        if (empty($connect)) {
            $connect = Config::get('database.default');
        }
        $db_config  = Config::get('database.connections.' . $connect);
        if (empty($db_config)) {
            throw new Exception("Exception: Database connection configuration does not exist");
        }
        if (!empty($dbname)) {
            $db_config['dbname'] = $dbname;
        }
        return $db_config;
    }

    /**
     * @Description If the parameter passed in is empty, use the default database connection, 
     *  otherwise use the passed in database connection, the format is: connection name.database name,
     *  such as: mysql.testdb
     *  If the parameter passed in does not have a connection symbol ".", it is considered to be the connection name.
     * @zh-cn: 如果传入的参数为空，则使用默认的数据库连接，否则使用传入的数据库连接，格式为：连接名.数据库名，如：mysql.testdb
     *  如果传入的参数没有连接符“.”，则认为是连接名，数据库名使用对应的配置项内容
     * @DateTime 2023-04-25
     * @param string $connect_and_dbnam like mysql.testdb or mysql
     * @return object
     */
    public static function link($connect_and_dbname = '')
    {
        $parts = explode('.', $connect_and_dbname, 2);
        if (count($parts) === 2) {
            self::$dbConfig = self::loadConfig($parts[0], $parts[1]);
            return self::getInstance();
        } else {
            self::$dbConfig = self::loadConfig($parts[0], '');
        }
        return self::getInstance();
    }

    public static function getInstance()
    {
        if (self::$db_instance == null) {
            self::$db_instance = new self;
        }
        return self::$db_instance;
    }

    public function table(string $table)
    {
        if (isset(self::$dbConfig['prefix_indexes']) && self::$dbConfig['prefix_indexes']) {
            $table = self::$dbConfig['prefix'] . $table;
        }
        $this->table = $this->fieldsSafe($table);
        return self::$db_instance;
    }

    // @zh-cn: 仅支持一维数组,请勿传入多维数组
    public function field(...$fields)
    {
        // Add backticks to the field name to avoid sql injection
        foreach ($fields as $key => $value) {
            if (empty($value)) continue;
            $fieldsSafe[$key] = $this->fieldsSafe($value);
        }
        if (isset($fieldsSafe)) {
            $this->field = implode(',', $fieldsSafe);
        }
        return self::$db_instance;
    }

    public function fieldsSafe()
    {
        $params = func_get_args();
        $fields = $params[0];

        if (is_array($fields)) {
            $fieldsSafe = '`' . implode('`,`', $fields) . '`';
        } else {
            $fieldsSafe = '`' . $fields . '`';
        }

        return $fieldsSafe;
    }

    public function join(string $table, string $condition, string $type = 'LEFT')
    {
        $this->join[] = " " . $type . " JOIN " . $table . " ON " . $condition;
        return self::$db_instance;
    }

    public function limit(...$limit)
    {
        $this->limit = " LIMIT " . implode(',', $limit);
        return self::$db_instance;
    }

    public function order(string $order)
    {
        if (!empty($order)) {
            $this->order = " ORDER BY " . $order;
        }
        return self::$db_instance;
    }

    public function where($where, $sep = '=', $value = '', $and = true)
    {
        if ($and) {
            $combineOperator = ' AND ';
        } else {
            $combineOperator = ' OR ';
        }

        // support custom default logical operator
        $sep = ' ' . strtoupper($sep) . ' ';

        if (is_array($where)) {
            foreach ($where as $key => $item) {
                // ready to combine the where condition
                $this->where .= $combineOperator;

                // if: support array like: [0 => ['id', '=', 1], 1 => ['name', 'test'], 2 => ['age' => 18]]]
                // else: support array like: [0 => 'id', 'key' => 'name']

                if (is_numeric($key) && is_array($item)) {
                    // support for array like ['id', '=', 1]
                    if (count($item) === 3) {
                        $this->where .= $this->fieldsSafe($item[0]) . ' ' . $item[1] . ' ' . $this->handleString($item[2]);
                    } elseif (count($item) === 2) {
                        // support for array like ['id', 1] or ['id', [1,2,3]]
                        if (is_array($item[1])) {
                            $this->where .= $this->fieldsSafe($item[0]) . ' IN (' . implode(',', $item[1]) . ')';
                        } else {
                            $this->where .= $this->fieldsSafe($item[0]) . $sep . $this->handleString($item[1]);
                        }
                    } elseif (count($item) === 1) {
                        // support for array like ['id' => 1]
                        $this->where .= $this->fieldsSafe(key($item)) . $sep . $this->handleString($item[key($item)]);
                    }
                } elseif (is_numeric($key) && !is_array($item)) {
                    // support for array like ['id = 1', 'name = test'], but note that it's not safe
                    $this->where .= $item;
                } else {
                    // support for array like ['id' => 1, 'name' => 'test']
                    $this->where .= $this->fieldsSafe($key) . $sep . $this->handleString($item);
                }
            }
        } else {
            if (!empty($value)) {
                // support for params like ('id', '=', 1) or ('id', '>', 1) or ('id', 'REGEXP', 'test')
                $this->where .= $combineOperator . $this->fieldsSafe($where) . $sep . $this->handleString($value);
            } else {
                // support for params like ('id = 1') or ('id = 1 OR id = 2'), but note that it's not safe
                $this->where .= $combineOperator . $where;
            }
        }
        $this->where = str_replace('(1 = 1)' . $combineOperator, '', $this->where);
        return self::$db_instance;
    }

    public function select()
    {
        $sql = $this->getSql();
        return $this->query($sql);
    }

    public function getAll()
    {
        return $this->select();
    }

    public function get()
    {
        $sql = $this->getSql();
        return $this->queryOne($sql);
    }

    public function first()
    {
        return $this->get();
    }

    public function getOne()
    {
        return $this->get();
    }

    public function find()
    {
        return $this->get();
    }

    public function count()
    {
        $sql = $this->getSql();
        return count($this->query($sql));
    }

    public function sum($field)
    {
        $sql = $this->getSql();
        $result = $this->queryOne($sql);
        return $result[$field];
    }

    public function exists()
    {
        $this->limit(1);
        $sql = $this->getSql();
        $result = $this->queryOne($sql);
        return !empty($result);
    }

    /**
     * @Description get collection by page and perPage
     * @zh-cn 根据页码和每页数量获取集合
     * @DateTime 2023-04-26
     * @param int $page - page number
     * @param int $perPage - number of items per page
     * @return array
     */
    public function paginate(int $page, int $perPage)
    {
        // Calculate the offset and limit values based on the page and perPage arguments
        $offset = ($page - 1) * $perPage;
        $limit = $perPage;
        $this->limit($offset, $limit);

        // Generate the SQL query with the offset and limit values
        $sql = $this->getSql();

        // Execute the query and return the results
        return $this->query($sql);
    }

    public function lastId()
    {
        return self::link()->db->lastInsertId() ?? 0;
    }

    public function last()
    {
        if (empty($this->table)) {
            throw new Exception("missing table name.");
        }
        $tableName = $this->table;
        $dbname = self::$dbConfig['dbname'];
        $sql = "SELECT column_name 
        FROM information_schema.columns 
        WHERE table_schema = '{$dbname}' AND table_name = '{$this->table}'";
        $key = $this->queryOne($sql)['column_name'] ?? '';
        if (empty($key)) {
            throw new Exception("missing primary key.");
        }
        $this->order($key . ' DESC');
        $this->table($tableName);
        return $this->get();
    }

    public function getConfig()
    {
        return self::$dbConfig;
    }

    public function insert(array $vars)
    {
        if (empty($this->table)) {
            throw new Exception("missing table name.");
        }
        $fields = '`' . implode('`,`', array_keys($vars)) . '`';
        $values = implode(',', array_fill(0, count($vars), '?'));
        $sql = "INSERT INTO {$this->table} ($fields) VALUES($values)";
        return $this->execute($sql, array_values($vars));
    }

    public function insertAll(array $vars)
    {
        // $vars like [['name' => 'test1'], ['name' => 'test2']]
        if (empty($this->table)) {
            throw new Exception("missing table name.");
        }
        $fields = '`' . implode('`,`', array_keys($vars[0])) . '`';
        $values = [];
        foreach ($vars as $item) {
            $values[] = '(' . implode(',', array_fill(0, count($item), '?')) . ')';
        }
        $values = implode(',', $values);
        $sql = "INSERT INTO {$this->table} ($fields) VALUES $values";
        $values = [];
        foreach ($vars as $item) {
            foreach ($item as $value) {
                $values[] = $value;
            }
        }
        return $this->execute($sql, $values);
    }

    public function update(array $vars)
    {
        if (empty($this->table)) {
            throw new Exception("missing table name.");
        }
        if (empty($this->where)) {
            throw new Exception("When updating or deleting, where is required.");
        }
        $sql = "UPDATE {$this->table} SET `" . implode('` = ?, `', array_keys($vars)) . "` = ? {$this->where}";
        return $this->execute($sql, array_values($vars));
    }

    public function updateAll(array $vars)
    {
        // $vars like [['name' => 'test1'], ['name' => 'test2']]
        if (empty($this->table)) {
            throw new Exception("missing table name.");
        }
        if (empty($this->where)) {
            throw new Exception("When updating or deleting, where is required.");
        }
        $fields = array_keys($vars[0]);
        $sql = "UPDATE {$this->table} SET `" . implode('` = CASE ' . $fields[0] . ' ', array_fill(0, count($fields), '?')) . "END {$this->where}";
        $values = [];
        foreach ($vars as $item) {
            foreach ($item as $value) {
                $values[] = $value;
            }
        }
        return $this->execute($sql, $values);
    }

    public function delete($key = '')
    {
        if (empty($this->table)) {
            throw new Exception("missing table name.");
        }
        if ($this->where == ' WHERE (1 = 1)' && $key !== true) {
            throw new Exception("Are you sure you want to delete all data? If you are sure, please pass in the parameter as true.");
        }
        $sql = "DELETE FROM {$this->table} {$this->where}";
        return $this->execute($sql);
    }

    public function deleteAll()
    {
        return $this->delete(true);
    }

    public function truncate()
    {
        if (empty($this->table)) {
            throw new Exception("missing table name.");
        }
        $sql = "TRUNCATE TABLE {$this->table}";
        return $this->execute($sql);
    }

    public function getFields()
    {
        if (empty($this->table)) {
            throw new Exception("missing table name.");
        }
        $dbname = self::$dbConfig['dbname'];
        $sql = "SELECT column_name 
        FROM information_schema.columns 
        WHERE table_schema = '{$dbname}' AND table_name = '{$this->table}'";
        $fields = $this->query($sql);
        $fields = array_column($fields, 'column_name');
        return $fields;
    }

    public function getPrimaryKey()
    {
        if (empty($this->table)) {
            throw new Exception("missing table name.");
        }
        $dbname = self::$dbConfig['dbname'];
        $sql = "SELECT column_name 
        FROM information_schema.columns 
        WHERE table_schema = '{$dbname}' AND table_name = '{$this->table}' AND column_key = 'PRI'";
        $key = $this->queryOne($sql)['column_name'] ?? '';
        return $key;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getTableStatus()
    {
        if (empty($this->table)) {
            throw new Exception("missing table name.");
        }
        $dbname = self::$dbConfig['dbname'];
        $sql = "SELECT * 
        FROM information_schema.tables 
        WHERE table_schema = '{$dbname}' AND table_name = '{$this->table}'";
        $status = $this->queryOne($sql);
        return $status;
    }

    public function getTables()
    {
        $dbname = self::$dbConfig['dbname'];
        $sql = "SELECT * 
        FROM information_schema.tables 
        WHERE table_schema = '{$dbname}'";
        $tables = $this->query($sql);
        return $tables;
    }

    public function getDatabases()
    {
        $sql = "SELECT * 
        FROM information_schema.schemata";
        $databases = $this->query($sql);
        return $databases;
    }

    public function getTableInfo()
    {
        if (empty($this->table)) {
            throw new Exception("missing table name.");
        }
        $dbname = self::$dbConfig['dbname'];
        $sql = "SELECT * 
        FROM information_schema.columns 
        WHERE table_schema = '{$dbname}' AND table_name = '{$this->table}'";
        $info = $this->query($sql);
        return $info;
    }

    public function query(string $sql, array $vars = [])
    {
        $stn = self::link()->db->prepare($sql);
        $stn->execute($vars);
        $row = $stn->fetchAll();
        $this->free();
        return $row;
    }

    public function queryOne(string $sql, array $vars = [])
    {
        $stn = self::link()->db->prepare($sql);
        $stn->execute($vars);
        $row = $stn->fetch();
        $this->free();
        return $row;
    }

    public function execute(string $sql, array $vars = [])
    {
        $stn = self::link()->db->prepare($sql);
        $stn->execute($vars);
        $row = $stn->rowCount(); // return affected rows
        $this->free();
        return $row;
    }

    public function getSql()
    {
        if (empty($this->table)) {
            throw new Exception("missing table name.");
        }
        if (is_array($this->join)) {
            $this->join = implode(' ', $this->join);
        }
        $sql = "SELECT {$this->field} FROM {$this->table}{$this->join}{$this->where}{$this->order}{$this->limit}";
        return $sql;
    }

    // Output sql query
    public function dd()
    {
        return $this->getSql();
    }

    // Release the content to avoid querying the last data
    protected function free()
    {
        $this->field = '*';
        $this->where = ' WHERE (1 = 1) ';
        $this->table = $this->limit = $this->order = '';
        $this->join = [];
    }

    /**
     * @Description avoid sql injection, Add double quotes to the value to avoid sql injection
     * @zh-cn: 将value值加上双引号，来避免sql注入
     * @DateTime 2023-05-17
     */
    protected function handleString($value)
    {
        if (is_string($value)) {
            $value = '"' . $value . '"';
        }
        return $value;
    }
}
