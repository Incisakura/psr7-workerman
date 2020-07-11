<?php

declare(strict_types=1);

namespace Psr7Workerman;

use Workerman\Protocols\Http\Response;

class Cookies
{
    /**
     * $_COOKIE
     *
     * @var string[][]
     */
    public static $cookies = [];

    /**
     * Cookies to send
     *
     * @var string[][]
     */
    public static $cookiesToSend = [];

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
        static::$cookiesToSend[$name] = func_get_args();
        return true;
    }

    /**
     * Sent cookie to Wokerman Response
     *
     * @param Response $response
     */
    public static function push(Response $response)
    {
        foreach (static::$cookiesToSend as $cookie) {
            $response->cookie(...$cookie);
        }
    }
}

/**
 * setcookie()
 *
 * @return true
 */
function set(
    string $name,
    string $value = '',
    int $expire = 0,
    string $path = '',
    string $domain = '',
    bool $secure = false,
    bool $http_only = false
) {
    return Cookies::set(...func_get_args());
}
