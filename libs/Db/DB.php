<?php

namespace libs\Db;

use PDO;
use PDOException;
use libs\Core\Message;

class DB implements DbInterface
{
    protected static $dbConfig;

    // Private a static variable to determine whether to instantiate
    private static $db_instance;

    private $table = '';
    private $where = 'WHERE (1 = 1)';
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
            Message::send(412, [], "Exception: " . $e->getMessage());
        }
    }

    public static function loadConfig($dbname)
    {
        global $_CONFIG;
        $db_config  = $_CONFIG['database']['connections'][$_CONFIG['database']['default']];
        if (!empty($dbname))
            $db_config['dbname'] = $dbname;
        return $db_config;
    }

    public static function link($dbname = '')
    {
        self::$dbConfig = self::loadConfig($dbname);
        return self::getInstance();
    }

    public static function getInstance()
    {
        if (self::$db_instance == null) {
            self::$db_instance = new self;
        }
        return self::$db_instance;
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
        return $stn->execute($vars)->rowCount();
    }

    public function table(string $table)
    {
        $this->table = $table;
        return self::$db_instance;
    }

    public function field(...$fields)
    {
        // Add backticks to the field name to avoid sql injection
        $this->field = '`' . implode('`,`', $fields) . '`';
        return self::$db_instance;
    }

    // Users can customize the field statement of the query, 
    // but it is not safe
    public function fieldString(string $fields)
    {
        $this->field = $fields;
        return self::$db_instance;
    }

    public function join(string $table, string $condition, string $type = 'LEFT')
    {
        $this->join[] = $type . " JOIN " . $table . " ON " . $condition;
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
        $this->where = str_replace('(1 = 1) AND', ' ', $this->where);
        return self::$db_instance;
    }

    // FIXME Alias can be more artisan
    // fetch all part
    public function select()
    {
        if (empty($this->table)) {
            Message::send(10400);
        }
        $sql = $this->getSql();
        return $this->query($sql);
    }

    public function getAll()
    {
        return $this->select();
    }

    // fetch one part
    public function get()
    {
        if (empty($this->table)) {
            Message::send(10400);
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
    // fetch part end

    public function insert(array $vars)
    {
        if (empty($this->table)) {
            Message::send(10400);
        }
        $fields = '`' . implode('`,`', array_keys($vars)) . '`';
        $values = implode(',', array_fill(0, count($vars), '?'));
        $sql = "INSERT INTO {$this->table} ($fields) VALUES($values)";
        return $this->execute($sql, array_values($vars));
    }

    public function update(array $vars)
    {
        if (empty($this->table)) {
            Message::send(10400);
        }
        if (empty($this->where)) {
            Message::send(10401);
        }
        $sql = "UPDATE {$this->table} SET " . implode('=?, ', array_keys($vars)) . "=? {$this->where}";
        return $this->execute($sql, array_values($vars));
    }

    public function delete()
    {
        if (empty($this->table)) {
            Message::send(10400);
        }
        if (empty($this->where)) {
            Message::send(10401);
        }
        $sql = "DELETE FROM {$this->table} {$this->where}";
        return $this->execute($sql);
    }

    public function count()
    {
        if (empty($this->table)) {
            Message::send(10400);
        }
        $sql = $this->getSql();
        return count($this->query($sql));
    }

    public function lastId()
    {
        return self::link()->db->lastInsertId() ?? 0;
    }

    public function getSql()
    {
        if (empty($this->table)) {
            Message::send(10400);
        }
        if (is_array($this->join)) {
            $this->join = implode(' ', $this->join);
        }
        $sql = "SELECT {$this->field} 
        FROM {$this->table}
        {$this->join}
        {$this->where}
        {$this->order}
        {$this->limit}";
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
