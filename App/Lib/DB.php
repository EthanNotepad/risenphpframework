<?php

namespace App\Lib;

use PDO;
use PDOException;

class DB
{
    protected static $dbCofig;

    // Private a static variable to determine whether to instantiate
    private static $db_instance;

    private $table = '';
    private $where = 'where (1 = 1)';
    private $limit = '';
    private $order = '';
    private $field = ' * ';
    private $join = [];

    private function __clone()
    {
        // Private a clone method to prevent cloning outside the object
    }

    public $db;

    protected function config()
    {

        return sprintf("mysql:host=%s;dbname=%s;chartset=%s;port=%s", self::$dbCofig['host'], self::$dbCofig['dbname'], self::$dbCofig['dbcharset'], self::$dbCofig['port']);
    }

    private function __construct()
    {
        try {
            self::$dbCofig = config('database.mysql');
            $this->db = new PDO($this->config(), self::$dbCofig['username'], self::$dbCofig['password']);
            // get only associative array
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            ApiOutput::ApiOutput("Exception: " . $e->getMessage(), 412);
        }
    }

    // Exposes a static method for easy invocation outside of an object
    public static function link()
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
        $result = $stn->fetchAll();
        $this->free();
        return $result;
    }

    public function queryOne(string $sql, array $vars = [])
    {
        $stn = self::link()->db->prepare($sql);
        $stn->execute($vars);
        $this->free();
        return $stn->fetch();
    }

    public function execute(string $sql, array $vars = [])
    {
        $stn = self::link()->db->prepare($sql);
        return $stn->execute($vars);
    }

    // Release the content to avoid querying the last data
    public function free()
    {
        $this->field = '*';
        $this->where = ' where (1 = 1) ';
        $this->table = $this->limit = $this->order = '';
        $this->join = [];
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
                $this->where .= 'and ';
                foreach ($item as $k => $v) {
                    if ($k == 2) {
                        if (is_string($v)) {
                            $v = '"' . $v . '"';
                        }
                    }
                    $this->where .= $v;
                }
            }
        } else {
            if (is_string($value)) {
                $value = '"' . $value . '"';
            }
            $this->where .= ' and ' . $where . ' ' . $sep . ' ' . $value;
        }
        $this->where = str_replace('(1 = 1) and', ' ', $this->where);
        return self::$db_instance;
    }

    protected function getSql()
    {
        if (empty($this->table)) {
            ApiOutput::ApiOutput([], 10400);
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

    public function get()
    {
        if (empty($this->table)) {
            ApiOutput::ApiOutput([], 10400);
        }
        $sql = $this->getSql();
        return $this->query($sql);
    }

    public function getOne()
    {
        if (empty($this->table)) {
            ApiOutput::ApiOutput([], 10400);
        }
        $sql = $this->getSql();
        return $this->queryOne($sql);
    }

    public function insert(array $vars)
    {
        if (empty($this->table)) {
            ApiOutput::ApiOutput([], 10400);
        }
        $fields = '`' . implode('`,`', array_keys($vars)) . '`';
        $values = implode(',', array_fill(0, count($vars), '?'));
        $sql = "INSERT INTO {$this->table} ($fields) VALUES($values)";
        return $this->execute($sql, array_values($vars));
    }

    public function update(array $vars)
    {
        if (empty($this->table)) {
            ApiOutput::ApiOutput([], 10400);
        }
        if (empty($this->where)) {
            ApiOutput::ApiOutput([], 10401);
        }
        $sql = "UPDATE {$this->table} SET " . implode('=?, ', array_keys($vars)) . "=? {$this->where}";
        return $this->execute($sql, array_values($vars));
    }

    public function delete()
    {
        if (empty($this->table)) {
            ApiOutput::ApiOutput([], 10400);
        }
        if (empty($this->where)) {
            ApiOutput::ApiOutput([], 10401);
        }
        $sql = "DELETE FROM {$this->table} {$this->where}";
        return $this->execute($sql);
    }

    public function count()
    {
        if (empty($this->table)) {
            ApiOutput::ApiOutput([], 10400);
        }
        $sql = $this->getSql();
        return count($this->query($sql));
    }

    // Output sql query, the parameter is the query method
    public function dd($method = '', $data = [])
    {
        switch ($method) {
            case 'get':
                $sql = $this->getSql();
                var_dump($sql);
                $this->free();
                break;
            case 'insert':
                if (empty($data)) {
                    ApiOutput::ApiOutput([], 10402);
                }
                $fields = '`' . implode('`,`', array_keys($data)) . '`';
                $values = implode(',', array_fill(0, count($data), '?'));
                $sql = "INSERT INTO {$this->table} ($fields) VALUES($values)";
                var_dump($sql);
                $this->free();
                break;
            default:
                print_r('Please pass in the correct method name');
                $this->free();
                break;
        }
        die;
    }
}
