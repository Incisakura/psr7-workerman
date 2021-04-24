<?php

declare(strict_types=1);

if (!function_exists('cookie_build')) {
    /**
     * Build cookie value string
     *
     * @param string $name
     * @param string $value
     * @param int $expire
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $http_only
     * @return string
     */
    function cookie_build(
        string $name,
        string $value = '',
        int $expire = 0,
        string $path = '',
        string $domain = '',
        bool $secure = false,
        bool $http_only = false,
        string $same_site = ''
    ) {
        $array = [
            $name . '=' . $value ?: '',
            'Expires=' . date(DATE_RFC7231, $expire),
            'Max-Age=' . max($expire - time(), 0),
            'Path=' . $path
        ];
        if ($domain) {
            $array[] = 'Domain=' . $domain;
        }
        if ($same_site) {
            $array[] = 'SameSite=' . $same_site;
        }
        if ($secure) {
            $array[] = 'Secure';
        }
        if ($http_only) {
            $array[] = 'HttpOnly';
        }
        return implode('; ', $array);
    }
}
