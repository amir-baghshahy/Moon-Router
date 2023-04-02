<?php

namespace src\core;

use Closure;
use Exception;

abstract class MoonRouterCore
{

    private array $redirected_routes = array();
    private array $routes = array();
    private array $params = array();


    /**
     * @param string $path
     * @param Closure|array $callback
     * @param string $method
     * @return void
     */
    protected function addRoute(string $path, Closure|array $callback, string $method): void
    {
        $this->routes[$method][$path] = $callback;

    }


    /**
     * @param string $from
     * @param string $to
     * @return void
     */
    protected function addRedirectRoute(string $from, string $to): void
    {
        $this->redirected_routes[$from] = $to;
    }


    /**
     * @param string $path
     * @return void
     */
    private function checkRedirectRoute(string $path): void
    {
        if (array_key_exists($path, $this->redirected_routes)) {
            MoonRouterHttp::redirectRoute($this->redirected_routes[$path]);
        }
    }


    /**
     * @return bool|string]
     * @throws Exception
     */
    private function resolveActionFromPath(): bool|Closure|array
    {
        try {
            $url = MoonRouterHttp::getUrlFromRequest();

            $this->checkRedirectRoute($url);
            foreach ($this->routes as $method => $key) {
                foreach ($key as $path => $callback) {
                    $regex = '~^(' . preg_replace('~\{([a-zA-Z0-9]+)\}~', '([^/]+)', $path) . ')$~';
                    preg_match($regex, $url, $match_url);
                    if ($match_url) {
                        $this->resolveparams($match_url, $path);
                        return $this->routes[MoonRouterHttp::getHttpMethod()][$path] ?? false;
                    }
                }
            }
            return false;
        } catch (Exception $e) {
            throw  new  Exception($e->getMessage());
        }
    }


    /**
     * @param array $match_url
     * @param string $path
     * @return void
     */
    private function resolveParams(array $match_url, string $path): void
    {
        try {
            preg_match_all('~\{([a-zA-z0-9]+)\}~', $path, $paramNames);
            $paramValues = array_slice($match_url, 2);
            unset($paramNames[0]);

            foreach ($paramNames[1] as $index => $key) {
                $this->params[$key] = $paramValues[$index];
            }
        } catch (Exception $e) {
            throw  new  Exception($e->getMessage());
        }
    }


    /**
     * @param array|Closure $action
     * @return mixed
     * @throws Exception
     */
    private function resolveAction(array|Closure $action): mixed
    {
        try {

            $__request = new MoonRouterHttp($_GET, $_POST, $_FILES, $_SERVER, $this->params);


            if ($action instanceof Closure) {
                return call_user_func($action, $__request);
            }

            if (class_exists($action[0])) {
                if (method_exists($action[0], $action[1])) {
                    $action[0] = new $action[0];
                    return call_user_func($action, $__request);
                } else throw new Exception("method {{$action[1]}}  not found in class {{$action[0]}} ");
            } else throw new Exception("class {{$action[0]}} not found");

        } catch (Exception $e) {
            throw  new  Exception($e->getMessage());
        }

    }


    /**
     * @return int|void
     * @throws Exception
     */
    protected function run()
    {
        try {
            $result = $this->resolveActionFromPath();

            if ($result == null) {
                return MoonRouterHttp::sendHttpCode(404);
            } elseif (is_bool($result) && !$result) {
                return MoonRouterHttp::sendHttpCode(405);
            }

            $this->resolveAction($result);

        } catch (Exception $e) {
            throw  new  Exception($e->getMessage());
        }

    }
}