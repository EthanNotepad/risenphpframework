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

        // Check if file data is present
        $postData = $this->ishaveFile();
        if ($postData === false) {
            // Set the post fields
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->body);
        } else {
            // Set the post fields with the file data
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function setHeaders($headers)
    {
        $this->headers = $headers;
        return $this;
    }

    public function setBody(array $body)
    {
        switch ($this->method) {
            case 'GET':
                $this->url .= '?' . http_build_query($body);
                break;
            default:
                $this->body = json_encode($body);
        }
        return $this;
    }

    public function setMethod($method)
    {
        $this->method = strtoupper($method);
        return $this;
    }

    public function ishaveFile()
    {
        // Check if file data is present
        $isHaveFile = false;
        if (is_array($this->body)) {
            $postData = array();
            foreach ($this->body as $key => $value) {
                // Check if the value is a file (assuming it's a string representing the file path)
                if (is_string($value) && file_exists($value)) {
                    $isHaveFile = true;
                    $file = new \CURLFile($value);
                    $postData[$key] = $file;
                } else {
                    $postData[$key] = $value;
                }
            }
        }
        if ($isHaveFile) {
            return $postData;
        } else {
            return $isHaveFile;
        }
    }

    // not sure whether need this
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
