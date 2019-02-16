<?php

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;
use React\Http\Server;
use React\MySQL\Factory;

require __DIR__ . '/vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();
$factory = new Factory($loop);
$db = $factory->createLazyConnection('root:@localhost/test');

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $routes) use ($db) {
    $routes->addRoute('GET', '/users', new \App\Controller\ListUsers($db));
    $routes->addRoute('POST', '/users', new \App\Controller\CreateUser($db));
    $routes->addRoute('GET', '/users/{id}', new \App\Controller\ViewUser($db));
    $routes->addRoute('PUT', '/users/{id}', new \App\Controller\UpdateUser($db));
    $routes->addRoute('DELETE', '/users/{id}', new \App\Controller\DeleteUser($db));
});

$server = new Server(function (ServerRequestInterface $request) use ($dispatcher) {
    $routeInfo = $dispatcher->dispatch(
        $request->getMethod(), $request->getUri()->getPath()
    );

    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            return new Response(404, ['Content-Type' => 'text/plain'],  'Not found');
        case FastRoute\Dispatcher::FOUND:
            $params = $routeInfo[2] ?? [];
            return $routeInfo[1]($request, ... array_values($params));
    }
});

$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);

$server->listen($socket);

$server->on('error', function (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
});

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . "\n";

$loop->run();
