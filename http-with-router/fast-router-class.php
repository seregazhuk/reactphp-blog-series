<?php

require __DIR__ . '/../vendor/autoload.php';

use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use React\Http\Server;
use React\Http\Response;
use React\EventLoop\Factory;
use Psr\Http\Message\ServerRequestInterface;

require __DIR__ . '/Router.php';

$tasks = [];
$listTasks = function () use (&$tasks) {
    return new Response(200, ['Content-Type' => 'text/plain'], implode(PHP_EOL, $tasks));
};

$addTask = function (ServerRequestInterface $request) use (&$tasks) {
    $task = $request->getParsedBody()['task'] ?? null;
    if ($task) {
        $tasks[] = $task;
        return new Response(201);
    }

    return new Response(400, ['Content-Type' => 'text/plain'], 'Task field is required');
};

$viewTask = function (ServerRequestInterface $request, $taskId) use (&$tasks) {
    if (isset($tasks[$taskId])) {
        return new Response(200, ['Content-Type' => 'text/plain'], $tasks[$taskId]);
    }

    return new Response(404, ['Content-Type' => 'text/plain'], 'Not found');
};

$routes = new RouteCollector(new Std(), new GroupCountBased());
$routes->get('/tasks', $listTasks);
$routes->get('/tasks/{id:\d+}', $viewTask);
$routes->post('/tasks', $addTask);

$loop = Factory::create();

$server = new Server(new Router($routes));

$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$server->listen($socket);
$server->on('error', function (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
});

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . "\n";

$loop->run();
