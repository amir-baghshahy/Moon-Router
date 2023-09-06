<?php

namespace src\core;

use Exception;

class MoonRouterCore
{
    private array $redirectedRoutes = [];
    private array $routes = [];
    private array $params = [];

    public function addRoute(string $path, $callback, string $method): void
    {
        $this->routes[$method][$path] = $callback;
    }

    public function addRedirectRoute(string $from, string $to): void
    {
        $this->redirectedRoutes[$from] = $to;
    }

    private function isRedirectRoute(string $path): bool
    {
        return array_key_exists($path, $this->redirectedRoutes);
    }

    private function resolveActionFromPath(string $url): mixed
    {
        foreach ($this->routes as $method => $routes) {
            foreach ($routes as $path => $callback) {
                $pattern = '~^(' . str_replace('{', '([^/]+)', preg_quote($path, '~')) . ')$~';
                if (preg_match($pattern, $url, $matches)) {
                    $this->resolveParams($matches, $path);
                    return $callback;
                }
            }
        }
        return false;
    }

    private function resolveParams(array $matches, string $path): void
    {
        preg_match_all('~\{([a-zA-Z0-9]+)\}~', $path, $paramNames);
        $paramNames = $paramNames[1];
        array_shift($matches); // Remove the full match

        foreach ($paramNames as $index => $paramName) {
            $this->params[$paramName] = $matches[$index];
        }
    }

    private function resolveAction($action): mixed
    {
        if (is_callable($action)) {
            return $action();
        }

        if (is_array($action) && count($action) === 2 && is_string($action[0]) && is_string($action[1])) {
            [$className, $methodName] = $action;

            if (class_exists($className)) {
                $classInstance = new $className();
                if (method_exists($classInstance, $methodName)) {
                    return $classInstance->$methodName();
                } else {
                    throw new Exception("Method '$methodName' not found in class '$className'");
                }
            } else {
                throw new Exception("Class '$className' not found");
            }
        }

        throw new Exception("Invalid action provided");
    }

    public function run(): void
    {
        $requestedUrl = MoonRouterHttp::getUrlFromRequest();

        if ($this->isRedirectRoute($requestedUrl)) {
            MoonRouterHttp::redirectRoute($this->redirectedRoutes[$requestedUrl]);
            return;
        }

        $action = $this->resolveActionFromPath($requestedUrl);

        if ($action === false) {
            MoonRouterHttp::sendHttpCode(404);
            return;
        }

        try {
            $this->resolveAction($action);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
