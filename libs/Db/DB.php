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

    public static function loadConfig($connect, $dbname)
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
     * @zh-CN: 如果传入的参数为空，则使用默认的数据库连接，否则使用传入的数据库连接，格式为：连接名.数据库名，如：mysql.testdb
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
        $this->table = $table;
        return self::$db_instance;
    }

    // @zh-CN: 仅支持一维数组,请勿传入多维数组
    public function field(...$fields)
    {
        // Add backticks to the field name to avoid sql injection
        foreach ($fields as $key => $value) {
            if (empty($value)) continue;
            if (is_array($value)) {
                $fieldsSafe[$key] = '`' . implode('`,`', $value) . '`';
            } else {
                $fieldsSafe[$key] = '`' . $value . '`';
            }
        }
        if (isset($fieldsSafe)) {
            $this->field = implode(',', $fieldsSafe);
        }
        return self::$db_instance;
    }

    /**
     * @Description Users can customize the field statement of the query, but it is not safe
     * @zh-CN: 用户可以自定义查询的字段语句，但是不安全
     * @param string $fields like 'r.id, r.name'
     * @return object
     */
    public function fieldString(string $fields)
    {
        $this->field = $fields;
        return self::$db_instance;
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
        $this->order = " ORDER BY " . $order;
        return self::$db_instance;
    }

    public function where($where, $sep = '', $value = '')
    {
        // Add double quotes to the value to avoid sql injection
        // 将value值加上双引号，来避免sql注入
        if (is_array($where)) {
            if (is_numeric(key($where))) {
                // @zh-CN: 二维数组
                foreach ($where as $item) {
                    $this->where .= ' AND ';
                    // equal 3 means that the user has specified the operator else use =
                    if (count($item) === 3) {
                        $this->where .= $item[0] . ' ' . $item[1] . ' ';
                        if (is_string($item[2])) {
                            $this->where .= '"' . $item[2] . '"';
                        } else {
                            $this->where .= $item[2];
                        }
                    } else {
                        $this->where .= $item[0] . ' = ';
                        if (is_string($item[1])) {
                            $this->where .= '"' . $item[1] . '"';
                        } else {
                            $this->where .= $item[1];
                        }
                    }
                }
            } else {
                // @zh-CN: 一维数组
                foreach ($where as $key => $value) {
                    $this->where .= ' AND ';
                    if (is_string($value)) {
                        $this->where .= $key . ' = "' . $value . '"';
                    } else {
                        $this->where .= $key . ' = ' . $value;
                    }
                }
            }
        } else {
            // if value not empty, means that user has specified the operator
            // @zh-CN: 如果第三个参数值不为空，则意味着用户定义了比较符（目前分别对1个参数，2个参数，3个参数的情况都做了处理）
            if (!empty($value)) {
                if (is_string($value)) {
                    $value = '"' . $value . '"';
                }
                $this->where .= ' AND ' . $where . ' ' . $sep . ' ' . $value;
            } elseif (!empty($sep)) {
                // if value is empty, means that user has not specified the operator
                // if sep is not empty, means that user has specified the value on the sep parameter
                if (is_string($sep)) {
                    $sep = '"' . $sep . '"';
                }
                $this->where .= ' AND ' . $where . ' = ' . $sep;
            } else {
                // if value and sep is empty, means that the user has customized the condition string of where, 
                // but note that this is a dangerous action because it is easy to be injected
                $this->where .= ' AND ' . $where;
            }
        }
        $this->where = str_replace('(1 = 1) AND ', '', $this->where);
        return self::$db_instance;
    }

    public function select()
    {
        if (empty($this->table)) {
            throw new Exception("Model error, missing table name.");
        }
        $sql = $this->getSql();
        return $this->query($sql);
    }

    public function getAll()
    {
        return $this->select();
    }

    public function get()
    {
        if (empty($this->table)) {
            throw new Exception("Model error, missing table name.");
        }
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
        if (empty($this->table)) {
            throw new Exception("Model error, missing table name.");
        }
        $sql = $this->getSql();
        return count($this->query($sql));
    }

    public function exists()
    {
        if (empty($this->table)) {
            throw new Exception("Model error, missing table name.");
        }
        $this->limit(1);
        $sql = $this->getSql();
        $result = $this->queryOne($sql);
        return !empty($result);
    }

    /**
     * @Description get collection by page and perPage
     * @zh-CN 根据页码和每页数量获取集合
     * @DateTime 2023-04-26
     * @param int $page - page number
     * @param int $perPage - number of items per page
     * @return array
     */
    public function paginate(int $page, int $perPage)
    {
        if (empty($this->table)) {
            throw new Exception("Model error, missing table name.");
        }

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
            throw new Exception("Model error, missing table name.");
        }
        $this->limit(1);
        $this->order('id DESC');
        $sql = $this->getSql();
        return $this->queryOne($sql);
    }

    public function getConfig()
    {
        return self::$dbConfig;
    }

    public function insert(array $vars)
    {
        if (empty($this->table)) {
            throw new Exception("Model error, missing table name.");
        }
        $fields = '`' . implode('`,`', array_keys($vars)) . '`';
        $values = implode(',', array_fill(0, count($vars), '?'));
        $sql = "INSERT INTO {$this->table} ($fields) VALUES($values)";
        return $this->execute($sql, array_values($vars));
    }

    public function update(array $vars)
    {
        if (empty($this->table)) {
            throw new Exception("Model error, missing table name.");
        }
        if (empty($this->where)) {
            throw new Exception("Model error, When updating or deleting, where is required.");
        }
        $sql = "UPDATE {$this->table} SET " . implode('=?, ', array_keys($vars)) . "=? {$this->where}";
        return $this->execute($sql, array_values($vars));
    }

    public function delete()
    {
        if (empty($this->table)) {
            throw new Exception("Model error, missing table name.");
        }
        if (empty($this->where)) {
            throw new Exception("Model error, When updating or deleting, where is required.");
        }
        $sql = "DELETE FROM {$this->table} {$this->where}";
        return $this->execute($sql);
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
            throw new Exception("Model error, missing table name.");
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
}
