<?php

namespace libs\Core;

class Response
{
    private $statusCode;
    private $headers;
    private $body;

    public function __construct($body, $statusCode = 200, $headers = [])
    {
        $this->body = $body;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public function send()
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $header) {
            header($header);
        }

        echo $this->body;
    }
}
