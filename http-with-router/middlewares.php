<?php

require __DIR__ . '/../vendor/autoload.php';

use React\Http\Server;
use React\Http\Response;
use React\EventLoop\Factory;
use Psr\Http\Message\ServerRequestInterface;

$loop = Factory::create();

$tasks = [];

$listTasks = function (ServerRequestInterface $request, callable $next) use (&$tasks) {
    if($request->getUri()->getPath() === '/tasks' && $request->getMethod() === 'GET') {
        return new Response(200, ['Content-Type' => 'text/plain'],  implode(PHP_EOL, $tasks));
    }

    return $next($request);
};

$addTask = function (ServerRequestInterface $request, callable $next) use (&$tasks) {
    if($request->getUri()->getPath() === '/tasks' && $request->getMethod() === 'POST') {
        $task = $request->getParsedBody()['task'] ?? null;
        if($task) {
            $tasks[] = $task;
            return new Response(201);
        }

        return new Response(400, ['Content-Type' => 'text/plain'], 'Task field is required');
    }

    return $next($request);
};

$notFound = function () {
    return new Response(404, ['Content-Type' => 'text/plain'],  'Not found');
};

$server = new Server([
    $listTasks,
    $addTask,
    $notFound
]);

$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . "\n";

$loop->run();
