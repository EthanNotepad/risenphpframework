<?php

namespace libs\Core;

class HttpRequest
{
    public $url;
    public $method;
    public $headers;
    public $body;

    public function __construct($url, $method = 'GET', $headers = [], $body = '')
    {
        $this->url = $url;
        $this->method = $method;
        $this->headers = $headers;
        $this->body = $body;
    }

    public function send()
    {
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->body);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function get()
    {
        $this->method = 'GET';
        return $this->send();
    }

    public function post()
    {
        $this->method = 'POST';
        return $this->send();
    }

    public function put()
    {
        $this->method = 'PUT';
        return $this->send();
    }

    public function delete()
    {
        $this->method = 'DELETE';
        return $this->send();
    }

    public function patch()
    {
        $this->method = 'PATCH';
        return $this->send();
    }

    public function head()
    {
        $this->method = 'HEAD';
        return $this->send();
    }

    public function options()
    {
        $this->method = 'OPTIONS';
        return $this->send();
    }

    public function trace()
    {
        $this->method = 'TRACE';
        return $this->send();
    }

    public function connect()
    {
        $this->method = 'CONNECT';
        return $this->send();
    }

    public function __toString()
    {
        return $this->send();
    }
}
