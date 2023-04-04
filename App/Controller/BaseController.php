<?php

namespace App\Controller;

class BaseController
{

    /**
     * Processing authority
     */
    public function __construct()
    {
        // do something
    }

    /**
     * Get URI elements.
     */
    protected function getUriSegments()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode('/', $uri);
        return $uri;
    }

    /**
     * Get querystring params.
     */
    protected function getQueryStringParams()
    {
        parse_str($_SERVER['QUERY_STRING'], $query);
        return $query;
    }

    /**
     * Get queryjson data.
     */
    protected function getQueryJson()
    {
        $queryJson = json_decode(file_get_contents('php://input'), true);
        if (is_null($queryJson)) {
            $queryJson = [];
        }
        return $queryJson;
    }
}
