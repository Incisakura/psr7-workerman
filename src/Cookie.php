<?php

declare(strict_types=1);

namespace Psr7Workerman;

use Workerman\Protocols\Http\Response;

class Cookie
{
    /**
     * Cookies to send to client
     *
     * @var string[][]
     */
    public static $cookies = [];

    /**
     * setcookie()
     */
    public static function set(
        string $name,
        string $value = '',
        int $expire = 0,
        string $path = '',
        string $domain = '',
        bool $secure = false,
        bool $http_only = false
    ) {
        static::$cookies[$name] = func_get_args();
        return true;
    }

    /**
     * Sent cookie to Wokerman Response
     *
     * @param Response $response
     */
    public static function push(Response $response)
    {
        foreach (static::$cookies as $cookie) {
            $response->cookie(...$cookie);
        }
    }
}

/**
 * setcookie()
 *
 * @return true
 */
function cookieset(
    string $name,
    string $value = '',
    int $expire = 0,
    string $path = '',
    string $domain = '',
    bool $secure = false,
    bool $http_only = false
) {
    return Cookie::set(...func_get_args());
}
