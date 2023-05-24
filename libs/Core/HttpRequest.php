<?php

namespace libs\Core;

class HttpRequest
{
    public $url;
    public $method;
    public $headers;
    public $body;

    public function __construct($url, $method = 'GET', $headers = [], $body = [])
    {
        $this->url = $url;
        $this->method = $method;
        $this->headers = $headers;
        $this->body = $body;
    }

    public function send()
    {
        // Initialize the curl
        $ch = curl_init($this->url);

        // Set the curl options
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);

        // Set the return type as string
        // @zh-cn: 设置以字符串形式将结果返回
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Check if file data is present
        $postData = $this->isHaveFile();
        if ($postData === false) {
            $this->headers[] = 'Content-Type: application/json';
            // Set the post fields with the json data
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->body));
        } elseif (is_array($postData)) {
            // Set the post fields with the file data
            $this->headers[] = 'Content-Type: multipart/form-data';
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        } else {
            // Set the post fields with the normal data, not used for now
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->body);
        }

        // Set the request headers
        if (!empty($this->headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        }

        // Execute the curl
        $response = curl_exec($ch);

        // Check for curl errors
        if (curl_errno($ch)) {
            throw new \Exception('Curl error: ' . curl_error($ch));
        }

        // Get the HTTP response code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Close the curl
        curl_close($ch);

        // Return the response
        return $response;
    }

    public function setHeaders($headers)
    {
        $this->headers = $headers;
        return $this;
    }

    // Note: $body is an array like ['key' => 'value']， Multidimensional arrays are not supported
    public function setBody(array $body)
    {
        switch ($this->method) {
            case 'GET':
                $this->url .= '?' . http_build_query($body);
                break;
            default:
                $this->body = $body;
        }
        return $this;
    }

    public function setMethod($method)
    {
        $this->method = strtoupper($method);
        return $this;
    }

    public function isHaveFile()
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
        return $isHaveFile ? $postData : false;
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
