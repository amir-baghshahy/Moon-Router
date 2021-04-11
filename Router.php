<?php

namespace Moon\Route;

use Closure;
use Exception;

class Router
{

    public array $routes = array();
    public array $params = array();

    public function get($path, $callback)
    {
        $this->addRoute($path, $callback, 'get');
    }

    public function post($path, $callback)
    {
        $this->addRoute($path, $callback, 'post');
    }

    public function put($path, $callback)
    {
        $this->addRoute($path, $callback, 'put');
    }


    public function delete($path, $callback)
    {
        $this->addRoute($path, $callback, 'delete');
    }


    public function any($path, $callback)
    {
        $this->addRoute($path, $callback, $this->getMethod());
    }

    public function addRoute($path, $callback, $method)
    {
        $this->routes[$method][$path] = $callback;
    }

    public function resolveUrlFromRequest(): string
    {
        $url =  $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($url, '?');

        if ($position === false) {
            return $url;
        }

        return substr($url, 0, $position);
    }


    public function getMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function findurlfrompath()
    {
        $url  = $this->resolveUrlFromRequest();

        foreach ($this->routes as $method => $key) {
            foreach ($key as $path => $callback) {
                $regex = '~^(' . preg_replace('~\{([a-zA-Z0-9]+)\}~', '([^/]+)', $path) . ')$~';
                preg_match($regex, $url, $matche_url);
                if ($matche_url) {
                    $this->resolveparams($matche_url, $path);
                    return $this->routes[$this->getMethod()][$path] ?? false;
                }
            }
        }
    }


    public function resolveparams(array $matchurl, string $path)
    {

        preg_match_all('~\{([a-zA-z0-9]+)\}~', $path, $paramNames);
        $paramvalues = array_slice($matchurl, 2);
        unset($paramNames[0]);

        foreach ($paramNames[1] as $index => $key) {
            $this->params[$key] = $paramvalues[$index];
        }

        return  $this->params;
    }


    public function resolveaction($action)
    {
        if (is_array($action)) {

            if (class_exists($action[0])) {
                if (method_exists($action[0], $action[1])) {
                    $action[0] = new $action[0];
                    call_user_func($action, $this->params);
                } else {
                    throw new Exception("method {{$action[1]}}  not found in class {{$action[0]}} ");
                }
            } else {
                throw new Exception("class {{$action[0]}} not found");
            }
        }

        if ($action instanceof Closure) {
            call_user_func($action, $this->params);
        }
    }

    public function http_code($code = null)
    {
        if ($code !== null) {

            switch ($code) {
                case 100:
                    $text = 'Continue';
                    break;
                case 101:
                    $text = 'Switching Protocols';
                    break;
                case 200:
                    $text = 'OK';
                    break;
                case 201:
                    $text = 'Created';
                    break;
                case 202:
                    $text = 'Accepted';
                    break;
                case 203:
                    $text = 'Non-Authoritative Information';
                    break;
                case 204:
                    $text = 'No Content';
                    break;
                case 205:
                    $text = 'Reset Content';
                    break;
                case 206:
                    $text = 'Partial Content';
                    break;
                case 300:
                    $text = 'Multiple Choices';
                    break;
                case 301:
                    $text = 'Moved Permanently';
                    break;
                case 302:
                    $text = 'Moved Temporarily';
                    break;
                case 303:
                    $text = 'See Other';
                    break;
                case 304:
                    $text = 'Not Modified';
                    break;
                case 305:
                    $text = 'Use Proxy';
                    break;
                case 400:
                    $text = 'Bad Request';
                    break;
                case 401:
                    $text = 'Unauthorized';
                    break;
                case 402:
                    $text = 'Payment Required';
                    break;
                case 403:
                    $text = 'Forbidden';
                    break;
                case 404:
                    $text = 'Not Found';
                    break;
                case 405:
                    $text = 'Method Not Allowed';
                    break;
                case 406:
                    $text = 'Not Acceptable';
                    break;
                case 407:
                    $text = 'Proxy Authentication Required';
                    break;
                case 408:
                    $text = 'Request Time-out';
                    break;
                case 409:
                    $text = 'Conflict';
                    break;
                case 410:
                    $text = 'Gone';
                    break;
                case 411:
                    $text = 'Length Required';
                    break;
                case 412:
                    $text = 'Precondition Failed';
                    break;
                case 413:
                    $text = 'Request Entity Too Large';
                    break;
                case 414:
                    $text = 'Request-URI Too Large';
                    break;
                case 415:
                    $text = 'Unsupported Media Type';
                    break;
                case 500:
                    $text = 'Internal Server Error';
                    break;
                case 501:
                    $text = 'Not Implemented';
                    break;
                case 502:
                    $text = 'Bad Gateway';
                    break;
                case 503:
                    $text = 'Service Unavailable';
                    break;
                case 504:
                    $text = 'Gateway Time-out';
                    break;
                case 505:
                    $text = 'HTTP Version not supported';
                    break;
                default:
                    exit('Unknown http status code "' . htmlentities($code) . '"');
                    break;
            }

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

            header($protocol . ' ' . $code . ' ' . $text);

            $GLOBALS['http_response_code'] = $code;
        } else {

            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
        }

        return $code;
    }



    public function run()
    {
        $result = $this->findurlfrompath();
        if (!is_bool($result) && $result != null) {
            $this->resolveaction($result);
        } elseif (is_bool($result) && $result == false) {
            return $this->http_code(405);
        } elseif (is_null($result)) {
            return $this->http_code(404);
        }
    }
}