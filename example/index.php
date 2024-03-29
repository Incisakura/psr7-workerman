<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;
use Psr7Workerman\Worker;

require __DIR__ . '/vendor/autoload.php';

// To run this file, you need to require slim/slim:^4.0

// Init app
$app = AppFactory::create();
$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response, $args) {
    // $_COOKIE is invaild. Please use $request->getCookieParams() to read cookies.
    // Please use $response->withAddedHeader('Set-Cookie', `cookie string`) to set cookie instead of \cookieset()
    $cookies = $request->getCookieParams();
    $name = $cookies['name'] ?? '';
    $response->getBody()->write('Hello World!!! ' . $name);
    return $response->withAddedHeader(...cookie_build('name', 'Sakura'));
});

// Init Worker
$worker = new Worker(
    // Only http
    'http://127.0.0.1:1234',
    // string: Class which implements `ServerRequestFactoryInterface`
    // object: Instance of `ServerRequestFactoryInterface`
    \Slim\Psr7\Factory\ServerRequestFactory::class,
    // Path for static file
    // All files would be treat as static file(including .php file)
    '/var/www/example',
    // context options for Wokerman
    []
);
$worker->count = 8; // Set number of process
// Set function
$loop = function (ServerRequestInterface $request) use ($app): ResponseInterface {
    return $app->handle($request);
};
$worker->onMessage($loop); // Function inject
$worker->runAll(); // Run!!!
