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
}