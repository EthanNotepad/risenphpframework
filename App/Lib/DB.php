<?php

namespace App\Lib;

use PDO;
use PDOException;

class DB
{
    // Private a static variable to determine whether to instantiate
    private static $db_instance;

    private function __clone()
    {
        // Private a clone method to prevent cloning outside the object
    }

    public $db;

    protected $options = [
        'table' => '',
        'field' => ' * ',
        'order' => '',
        'limit' => '',
        'where' => ''
    ];
    protected function config()
    {
        return sprintf("mysql:host=%s;dbname=%s;chartset=%s;port=%s", DB_HOST, DB_NAME, DB_CHARSET, DB_PORT);
    }

    private function __construct()
    {
        try {
            $this->db = new PDO($this->config(), DB_USER, DB_PASS);
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
        // can update function(not online@sheng)
        // $result = (count($result) === 1) ? $result[0] : $result;
        return $result;
    }

    public function queryOne(string $sql, array $vars = [])
    {
        $stn = self::link()->db->prepare($sql);
        $stn->execute($vars);
        return $stn->fetch();
    }

    public function execute(string $sql, array $vars = [])
    {
        $stn = self::link()->db->prepare($sql);
        return $stn->execute($vars);
    }

    public function table(string $table)
    {
        // Release the content of the option to avoid querying the last data
        $this->options['field'] = ' * ';
        $this->options['order'] = '';
        $this->options['limit'] = '';
        $this->options['where'] = '';
        $this->options['table'] = $table;
        return $this;
    }

    public function field(...$fields)
    {
        $this->options['field'] = '`' . implode('`,`', $fields) . '`';
        return $this;
    }

    public function limit(...$limit)
    {
        $this->options['limit'] = " LIMIT " . implode(',', $limit);
        return $this;
    }

    public function order(string $order)
    {
        $this->options['order'] = " ORDER BY " . $order;
        return $this;
    }

    public function where(string $where)
    {
        if (empty($where)) {
            ApiOutput::ApiOutput([], 10404);
        }
        $this->options['where'] = " WHERE " .  $where;
        return $this;
    }

    public function get()
    {
        if (empty($this->options['table'])) {
            ApiOutput::ApiOutput([], 10400);
        }
        $sql = "SELECT {$this->options['field']} FROM
        {$this->options['table']} {$this->options['where']}
        {$this->options['order']} {$this->options['limit']}";
        return $this->query($sql);
    }

    public function insert(array $vars)
    {
        if (empty($this->options['table'])) {
            ApiOutput::ApiOutput([], 10400);
        }
        $fields = '`' . implode('`,`', array_keys($vars)) . '`';
        $values = implode(',', array_fill(0, count($vars), '?'));
        $sql = "INSERT INTO {$this->options['table']} ($fields) VALUES($values)";
        return $this->execute($sql, array_values($vars));
    }

    public function update(array $vars)
    {
        if (empty($this->options['table'])) {
            ApiOutput::ApiOutput([], 10400);
        }
        if (empty($this->options['where'])) {
            ApiOutput::ApiOutput([], 10401);
        }
        $sql = "UPDATE {$this->options['table']} SET " . implode('=?, ', array_keys($vars)) . "=? {$this->options['where']}";
        return $this->execute($sql, array_values($vars));
    }

    public function delete()
    {
        if (empty($this->options['table'])) {
            ApiOutput::ApiOutput([], 10400);
        }
        if (empty($this->options['where'])) {
            ApiOutput::ApiOutput([], 10401);
        }
        $sql = "DELETE FROM {$this->options['table']} {$this->options['where']}";
        return $this->execute($sql);
    }

    public function count()
    {
        if (empty($this->options['table'])) {
            ApiOutput::ApiOutput([], 10400);
        }
        $sql = "SELECT count(*) as count FROM
        {$this->options['table']} {$this->options['where']}";
        $stn = self::link()->db->prepare($sql);
        $stn->execute();
        $row = $stn->fetch(PDO::FETCH_ASSOC);
        return  $row['count'];
    }

    // Output sql query, the parameter is the query method
    public function dd($method = '', $data = [])
    {
        switch ($method) {
            case 'get':
                $sql = "SELECT {$this->options['field']} FROM
                        {$this->options['table']} {$this->options['where']}
                        {$this->options['order']} {$this->options['limit']}";
                var_dump($sql);
                break;
            case 'insert':
                if (empty($data)) {
                    ApiOutput::ApiOutput([], 10402);
                }
                $fields = '`' . implode('`,`', array_keys($data)) . '`';
                $values = implode(',', array_fill(0, count($data), '?'));
                $sql = "INSERT INTO {$this->options['table']} ($fields) VALUES($values)";
                var_dump($sql);
                break;
            default:
                print_r('Please pass in the correct method name');
                break;
        }
        die;
    }
}
