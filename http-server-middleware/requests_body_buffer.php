<?php
require __DIR__ . '/../vendor/autoload.php';

use React\Http\Middleware\RequestBodyBufferMiddleware;
use React\Http\Response;
use React\EventLoop\Factory;
use Psr\Http\Message\ServerRequestInterface;

$loop = Factory::create();

$server = new \React\Http\StreamingServer([
    new RequestBodyBufferMiddleware(1),
    function (ServerRequestInterface $request) {
        return new Response(200, ['Content-Type' => 'text/plain'],  "Hello world\n");
    }
]);

$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;

$loop->run();
