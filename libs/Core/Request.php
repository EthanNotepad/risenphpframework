<?php

namespace libs\Core;

class Request
{
    private $params;
    private $method;
    private $protocol;
    private $host;
    private $path;
    private $query;

    public function __construct()
    {
        $this->params = $_REQUEST;
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->protocol = $_SERVER['SERVER_PROTOCOL'];
        $this->host = $_SERVER['HTTP_HOST'];
        $this->path = $_SERVER['REQUEST_URI'];
        $this->query = $_SERVER['QUERY_STRING'];
    }

    public function param()
    {
        return $this->params;
    }

    public function method()
    {
        return $this->method;
    }

    public function header($key = '')
    {
        $headers = getallheaders();
        if (empty($key)) {
            return $headers;
        }
        if (array_key_exists($key, $headers)) {
            return $headers[$key];
        }
        return null;
    }

    public function protocol()
    {
        return $this->protocol;
    }

    public function host()
    {
        return $this->host;
    }

    public function path()
    {
        return $this->path;
    }

    public function getUriSegments()
    {
        return explode('/', parse_url($this->path, PHP_URL_PATH));;
    }

    public function query()
    {
        return $this->query;
    }

    public function input($isArray = true)
    {
        $queryRaw = file_get_contents('php://input');
        if ($isArray) {
            $queryJson = json_decode($queryRaw, true);
            if (is_null($queryJson)) {
                $queryJson = [];
            }
            return $queryJson;
        }
        return json_decode($queryRaw);
    }

    public function get(string $key = '')
    {
        $getData = $_GET;
        if (!empty($key)) {
            if (array_key_exists($key, $getData)) {
                return $getData[$key];
            } else {
                return null;
            }
        }
        return $getData;
    }

    public function post(string $key = '')
    {
        $postData = $_POST;
        if (!empty($key)) {
            if (array_key_exists($key, $postData)) {
                return $postData[$key];
            } else {
                return null;
            }
        }
        return $postData;
    }

    public function file(string $key = '')
    {
        $files = $_FILES;
        if (!empty($key)) {
            if (array_key_exists($key, $files)) {
                return $files[$key];
            } else {
                return null;
            }
        }
        return $files;
    }

    public function getFiles(string $key = '')
    {
        return $this->file($key);
    }

    public function getBody()
    {
        return $this->input();
    }

    public function contentType()
    {
        return $this->header('Content-Type');
    }

    public function isAjax()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return true;
        }
        return false;
    }
}
