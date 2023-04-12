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

    public function getParams()
    {
        return $this->params;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getProtocol()
    {
        return $this->protocol;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getUriSegments()
    {
        return explode('/', parse_url($this->path, PHP_URL_PATH));;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getData()
    {
        $rawData = file_get_contents('php://input');
        $jsonData = json_decode($rawData);
        return $jsonData;
    }

    public function post(string $key = '')
    {
        $postData = $this->params;
        if (!empty($key)) {
            if (array_key_exists($key, $postData)) {
                return $postData[$key];
            } else {
                return null;
            }
        }
        return $postData;
    }
}
