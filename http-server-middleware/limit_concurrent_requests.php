<?php
require '../vendor/autoload.php';

use React\Http\Server;
use React\Http\Response;
use React\EventLoop\Factory;
use Psr\Http\Message\ServerRequestInterface;

$loop = Factory::create();

$server = new Server([
    new \React\Http\Middleware\LimitConcurrentRequestsMiddleware(1),
    function(ServerRequestInterface $request, callable $next) use ($loop) {
        $deferred = new \React\Promise\Deferred();
        $loop->addTimer(2, function() use ($next, $request, $deferred) {
            echo 'Resolving request' . PHP_EOL;
            $deferred->resolve($next($request));
        });

        return $deferred->promise();
    },
    function (ServerRequestInterface $request) {
        return new Response(200, ['Content-Type' => 'text/plain'],  "Hello world\n");
    }
]);

$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;

$loop->run();
