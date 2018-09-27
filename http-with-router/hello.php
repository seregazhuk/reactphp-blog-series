<?php

require __DIR__ . '/../vendor/autoload.php';

use React\Http\Server;
use React\Http\Response;
use React\EventLoop\Factory;
use Psr\Http\Message\ServerRequestInterface;

$loop = Factory::create();

$tasks = [];
$server = new Server(function (ServerRequestInterface $request) use (&$tasks) {
    $path = $request->getUri()->getPath();
    $method = $request->getMethod();

    if($path === '/tasks') {
        if($method === 'GET') {
            return new Response(200, ['Content-Type' => 'text/plain'],  implode(PHP_EOL, $tasks));
        }

        if($method === 'POST') {
            $task = $request->getParsedBody()['task'] ?? null;
            if($task) {
                $tasks[] = $task;
                return new Response(201);
            }

            return new Response(400, ['Content-Type' => 'text/plain'], 'Task field is required');
        }
    }

    return new Response(404, ['Content-Type' => 'text/plain'],  'Not found');
});

$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . "\n";

$loop->run();
