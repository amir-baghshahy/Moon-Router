<?php

namespace src\core;

use JetBrains\PhpStorm\NoReturn;

final class MoonRouterHttp
{

    private static $query_param = [];
    private static $routeParams = [];
    private static $form_data = [];
    private static $files = [];
    private static $server = [];

    public function __construct($query_param, $form_data, $files, $server, $routeParams)
    {
        self::$query_param = $query_param;
        self::$routeParams = $routeParams;
        self::$form_data = $form_data;
        self::$files = $files;
        self::$server = $server;
    }


    public function __debugInfo(): array
    {
        return [
            'routeParams' => self::$routeParams,
            'queryParams' => self::$query_param,
            'formInputs' => self::$form_data,
            'files' => self::$files,
        ];
    }

    /**
     * @return string
     */
    public static function getUrlFromRequest(): string
    {
        $url = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($url, '?');
        if ($position === false) {
            return $url;
        }


        return substr($url, 0, $position);
    }


    /**
     * check if request method get
     * @return bool
     */
    public static function isGet(): bool
    {
        return self::getHttpMethod() == 'get';
    }

    /**
     * check if request method post
     * @return bool
     */
    public static function isPost(): bool
    {
        return self::getHttpMethod() == 'post';
    }

    /**
     * get query param from request
     * @param $key
     * @return mixed|void
     */
    public static function get($key)
    {
        self::$query_param = $_GET;

        if (isset(self::$query_param[$key])) {
            return self::$query_param[$key];
        }
    }


    /**
     * get route param from request
     * @param $key
     * @return mixed|void
     */
    public static function param($key)
    {
        if (isset(self::$routeParams[$key])) {
            return self::$routeParams[$key];
        }
    }


    /**
     * get form data param from request
     * @param $key
     * @return mixed|void
     */
    public static function post($key)
    {
        self::$form_data = $_POST;

        if (isset(self::$form_data [$key])) {
            return self::$form_data [$key];
        }
    }


    /**
     * get file  from request
     * @param $key
     * @return mixed|void
     */
    public static function file($key)
    {
        self::$files = $_FILES;

        if (isset(self::$files[$key])) {
            return self::$files[$key];
        }
    }


    /**
     * get global $_SERVER from request
     * @param $key
     * @return mixed|void
     */
    public static function server($key)
    {
        self::$server = $_SERVER;

        if (isset(self::$server [$key])) {
            return self::$server [$key];
        }
    }

    /**
     * @return false|string
     */
    public function __tostring()
    {
        return json_encode([
            'routeParams' => self::$routeParams,
            'queryParams' => self::$query_param,
            'formInputs' => self::$form_data,
            'files' => self::$files,
        ]);
    }

    /**
     * get array of bodt in request with key and value
     * @return array
     */
    public static function getBody(): array
    {
        $data = [];
        if (self::isGet()) {
            foreach ($_GET as $key => $value) {
                $data[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if (self::isPost()) {
            foreach ($_POST as $key => $value) {
                $data[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        return $data;
    }


    /**
     * redirect route
     * @param string $route
     * @param int $code
     * @return void
     */
    #[NoReturn] public static function redirectRoute(string $route, int $code = 302)
    {
        header("Location:" . $route, true, $code);
        exit();
    }

    /**
     * get REQUEST_METHOD from request
     * @return string
     */
    public static function getHttpMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }


    /**
     * send http status code
     * @param int|null $code
     * @return int
     */
    public static function sendHttpCode(int|null $code = null): int
    {
        if ($code === null) {
            $code = ($GLOBALS['http_response_code'] ?? 200);
        }

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
        }

        $protocol = ($_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.0');

        header($protocol . ' ' . $code . ' ' . $text);

        $GLOBALS['http_response_code'] = $code;

        return $code;
    }

}