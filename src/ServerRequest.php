<?php

declare(strict_types=1);

namespace Psr7Workerman;

use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Workerman\Protocols\Http\Request;

class ServerRequest
{
    /**
     * ServerRequestFactory class group
     *
     * @var array
     */
    public const SERVER_REQUEST_CREATORS = [
        'guzzle'    => 'GuzzleHttp\Psr7\HttpFactory',
        'laminas'   => 'Laminas\Diactoros\ServerRequestFactory',
        'slim'      => 'Slim\Psr7\Factory\ServerRequestFactory',
    ];

    /** @var ServerRequestFactoryInterface */
    public static $serverRequestFactory;

    /**
     * Create `ServerRequest` from factory and return
     *
     * @param Request WorkerMan Request
     * @return ServerRequestInterface
     */
    public static function getServerRequest(Request $request): ServerRequestInterface
    {
        $auth = static::getAuth($request->header('Authorization'));
        $uri = 'http://' . $auth . '@' . $request->host() . $request->uri();
        /** @var array */
        $headers = $request->header();
        /** @var array */
        $cookies = $request->cookie();
        /** @var array */
        $uploadFiles = $request->file();
        $serverRequest = static::$serverRequestFactory
            ->createServerRequest($request->method(), $uri)
            ->withCookieParams($cookies)
            ->withUploadedFiles($uploadFiles);
        foreach ($headers as $name => $value) {
            $serverRequest = $serverRequest->withHeader($name, $value);
        }
        return $serverRequest;
    }

    /**
     * Get basic auth
     *
     * @param string|null $auth
     * @return string
     */
    private static function getAuth(?string $auth): string
    {
        if (
            $auth === null
            || count($auth = preg_split('!\s+!', $auth)) != 2
            || ucfirst($auth[0]) != 'Basic'
            || ($auth = base64_decode($auth[1])) == false
            || ($xauth = explode(':', $auth, 2)) === false
            || count($xauth) != 2
        ) {
            return ':';
        }
        return $auth;
    }

    /**
     * Set static serverRequestFactory
     *
     * @param string|object $project
     * @throws \RuntimeException
     */
    public static function setServerRequestFactory($project): void
    {
        $serverRequestCreators = static::SERVER_REQUEST_CREATORS;

        if ($project instanceof ServerRequestFactoryInterface) {
            static::$serverRequestFactory = $project;
            return;
        }

        if ($project != '' && array_key_exists($project, $serverRequestCreators)) {
            static::$serverRequestFactory = new $serverRequestCreators[$project]();
            return;
        }

        if (class_exists($project) && is_a($project, ServerRequestFactoryInterface::class, true)) {
            static::$serverRequestFactory = new $project();
            return;
        }

        foreach ($serverRequestCreators as $serverRequestCreator) {
            if (class_exists($serverRequestCreator)) {
                static::$serverRequestFactory = new $serverRequestCreator();
                return;
            }
        }

        throw new \RuntimeException('Could not detect any available PSR-17 ResponseFactory implementations.');
    }
}
