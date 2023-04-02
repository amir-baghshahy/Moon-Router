<?php

namespace src;


use src\core\MoonRouterCore;
use src\core\MoonRouterHttp;

class MoonRouter extends MoonRouterCore
{
    /**
     * @method static \src\core\MoonRouterCore get(string $uri, array|string|callable|null $action = null)
     * @method static \src\core\MoonRouterCore post(string $uri, array|string|callable|null $action = null)
     * @method static \src\core\MoonRouterCore put(string $uri, array|string|callable|null $action = null)
     * @method static \src\core\MoonRouterCore patch(string $uri, array|string|callable|null $action = null)
     * @method static \src\core\MoonRouterCore delete(string $uri, array|string|callable|null $action = null)
     * @method static \src\core\MoonRouterCore options(string $uri, array|string|callable|null $action = null)
     * @method static \src\core\MoonRouterCore any(string $uri, array|string|callable|null $action = null)
     * @method static \src\core\MoonRouterCore redirect(string $uri, string $destination, int $status = 302)
     * @method static \src\core\MoonRouterCore permanentRedirect(string $uri, string $destination)
     */


    /**
     * @param string $method
     * @param array $parameters
     * @return void
     * @throws \Exception
     */
    public static function __callStatic(string $method, array $parameters)
    {
        $instance = new MoonRouter();

        if ($method === 'redirect' || $method == 'permanentRedirect') {
            $instance->addRedirectRoute($parameters[0], $parameters[1]);
        } else {
            $instance->addRoute($parameters[0], $parameters[1], $method != 'any' ? $method : MoonRouterHttp::getHttpMethod());
        }

        $instance->run();
    }

}