<?php

declare(strict_types=1);

if (!function_exists('cookie_build')) {
    /**
     * Construct value for `$response->withAddedHeader()`. \
     * See https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie. \
     * Example: `$response->withAddedHeader(...cookie_build('name', 'value));`
     *
     * @param string    $name       Cookie name
     * @param string    $value      Cookie value
     * @param int       $expires    Unix timestamp expires (0 for browser session)
     * @param string    $path       Cookie path
     * @param string    $domain     Cookie domain
     * @param bool      $secure     Only in https
     * @param bool      $http_only  Not accessable by JavaScript
     * @param string    $same_site  Same site flag
     *
     * @return array `['Set-Cookie', {value}]`
     */
    function cookie_build(
        string $name,
        string $value = '',
        int $expires = 0,
        string $path = '',
        string $domain = '',
        bool $secure = false,
        bool $http_only = false,
        string $same_site = ''
    ): array {
        $data = ["$name=$value"];
        if ($expires > 0) {
            $data[] = 'Expires=' . date(DATE_RFC7231, $expires);
            $data[] = 'Max-Age=' . (string) max(0, $expires - time());
        }
        if ($path != '') {
            $data[] = "Path=$path";
        }
        if ($domain != '') {
            $data[] = "Domain=$domain";
        }
        if ($secure) {
            $data[] = 'Secure';
        }
        if ($http_only) {
            $data[] = 'HttpOnly';
        }
        if ($same_site) {
            $data[] = "SameSite=$same_site";
        }
        return ['Set-Cookie', implode('; ', $data)];
    }
}
