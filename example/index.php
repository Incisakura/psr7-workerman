<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;
use Psr7Workerman\Worker;

// ↓ Import function for set cookie ↓

use function Psr7Workerman\cookieset;

require __DIR__ . '/vendor/autoload.php';

// Init app
$app = AppFactory::create();
$app->get('/', function ($request, $response, $args) {
    cookieset('name', '233'); // Set cookies, setcookie() is invalid.
    // You can also use it by Psr7Workerman\Cookie::set().
    // Use $_COOKIE to get client cookie.
    $name = $_COOKIE['name'];
    $response->getBody()->write('Hello World!!!' . $name);
    return $response;
});

// Init Worker
$worker = new Worker(
    // Only http
    'http://127.0.0.1:1234',
    // string: slim / guzzle / laminas
    //         Class which implements `ServerRequestFactoryInterface`
    // object: Instance of `ServerRequestFactoryInterface`
    'slim',
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
