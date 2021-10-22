<?php

declare(strict_types=1);

namespace Psr7Workerman;

use Workerman\Connection\ConnectionInterface;
use Workerman\Protocols\Http\Request;
use Workerman\Protocols\Http\Response;
use Workerman\Worker as WorkermanWorker;

class Worker extends WorkermanWorker
{
    /** @var string */
    protected $static_root;

    /**
     * Worker Constructor
     *
     * @param string        $socket_name        http schema
     * @param string|object $psr7               PSR-7 Project to construct
     * @param string        $static_root        Root of static file
     * @param string        $context_option     Context option for workerman
     */
    public function __construct(
        string $socket_name = 'http://127.0.0.1:233',
        string $psr17 = '',
        string $static_root = '',
        array $context_option = []
    ) {
        parent::__construct($socket_name, $context_option);
        ServerRequest::setServerRequestFactory($psr17);
        $this->static_root = rtrim($static_root, '/\\');
    }

    /**
     * Workerman onMessage event
     *
     * @param callable $call (`ServerRequestInterface`): `ResponseInterface`
     */
    public function onMessage(callable $call)
    {
        $this->onMessage = function (ConnectionInterface $connection, Request $data) use ($call) {
            $path = $data->path();
            if ($this->static_root != '' && is_file($this->static_root . $path)) {
                $wmResponse = new Response();
                $wmResponse->withFile($this->static_root . $path);
            } else {
                // PSR-7 event
                $request = ServerRequest::getServerRequest($data);
                /** @var \Psr\Http\Message\ResponseInterface */
                $response = $call($request);

                $wmResponse = new Response(
                    $response->getStatusCode(),
                    $response->getHeaders(),
                    $response->getBody()->__toString()
                );
            }
            $connection->send($wmResponse);
        };
    }
}
