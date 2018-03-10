<?php

require '../vendor/autoload.php';

use React\Http\Server;
use React\Http\Response;
use React\EventLoop\Factory;
use Psr\Http\Message\ServerRequestInterface;

$tasks = [];
$listTasks = function () use ($tasks) {
    return new Response(200, ['Content-Type' => 'text/plain'],  implode(PHP_EOL, $tasks));
};

$addTask = function (ServerRequestInterface $request) use (&$tasks) {
    $task = $request->getParsedBody()['task'] ?? null;
    if($task) {
        $tasks[] = $task;
        return new Response(201);
    }

    return new Response(400, ['Content-Type' => 'text/plain'], 'Task field is required');
};

$viewTask = function(ServerRequestInterface $request) use ($tasks) {
    $path = $request->getUri()->getPath();
    die();
};

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $routes) use ($listTasks, $addTask, $viewTask) {
    $routes->addRoute('GET', '/tasks', $listTasks);
    $routes->addRoute('GET', '/tasks/{id:\d+}', $viewTask);
    $routes->addRoute('POST', '/tasks', $addTask);
});

$loop = Factory::create();

$server = new Server(function (ServerRequestInterface $request) use ($dispatcher) {
    $routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            return new Response(404, ['Content-Type' => 'text/plain'],  'Not found');
        case FastRoute\Dispatcher::FOUND:
            return $routeInfo[1]($request);
    }

    return new Response(200, ['Content-Type' => 'text/plain'], 'Tasks list');
});

$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$server->listen($socket);
$server->on('error', function(Exception $e){
    echo $e->getMessage() . PHP_EOL;
});

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . "\n";

$loop->run();
