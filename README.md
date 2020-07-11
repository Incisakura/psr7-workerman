# Psr7Workermam

---

> This project is on alpha.
>
> Workerman is an asynchronous event-driven PHP framework with high performance to build fast and scalable network applications. Workerman supports HTTP, Websocket, SSL and other custom protocols. Workerman supports event extension.

## What dose it do

It passes all http varaibles from Workerman framework to target PSR-7 project which makes you could focus on PSR-7.

## Hello World with Slim

``` php
<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;
use Psr7Workerman\Worker;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
$app->get('/', function ($request, $response, $args) {
    $response->getBody()->write('Hello World!!!');
    return $response;
});
$worker = new Worker('http://127.0.0.1:1234');
$worker->onMessage(function (ServerRequestInterface $request) use ($app): ResponseInterface {
    return $app->handle($request);
});
$worker->runAll();
```

## License

[MIT License with SATA License](https://github.com/Incisakura/Psr7Workerman/blob/master/LICENSE.md)

`SATA` License (The Star And Thank Author License) : 
> By using this project(including code/docs/...), you shall star/+1/like the project(s) in project url section above first, and then thank the author(s) in Copyright section.
