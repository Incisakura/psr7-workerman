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
    protected $staticRoot;

    /**
     * Worker Constructor
     *
     * @param string        $socket_name        http schema
     * @param string|object $psr7               PSR7 Project to construct
     * @param string        $staticRoot         Root of static file
     * @param string        $context_option     Context option for workerman
     */
    public function __construct(
        string $socket_name = 'http://127.0.0.1:233',
        $psr7 = '',
        string $staticRoot = '',
        array $context_option = []
    ) {
        parent::__construct($socket_name, $context_option);
        ServerRequest::setServerRequestFactory($psr7);
        $this->staticRoot = rtrim($staticRoot, '/\\');
    }

    /**
     * Workerman onMessage event
     *
     * @param callable $call With param `ServerRequestInterface`
     */
    public function onMessage(callable $call)
    {
        $this->onMessage = function (ConnectionInterface $connection, Request $data) use ($call) {
            $path = $data->path();
            if ($this->staticRoot != '' && is_file($this->staticRoot . $path)) {
                $wmResponse = new Response();
                $wmResponse->withFile($this->staticRoot . $path);
            } else {
                // PSR-7 event
                Cookies::$cookies = $data->cookie();
                $request = ServerRequest::getServerRequest($data);
                $response = $call($request);

                // Workerman Response construct & send
                $wmResponse = new Response(
                    $response->getStatusCode(),
                    $response->getHeaders(),
                    $response->getBody()->__toString()
                );
                Cookies::push($wmResponse);
            }
            $connection->send($wmResponse);
        };
    }
}
