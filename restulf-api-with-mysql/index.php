<?php

use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;
use React\Http\Server;
use React\MySQL\Factory;

require __DIR__ . '/vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();
$factory = new Factory($loop);
$db = $factory->createLazyConnection('root:@localhost/reactphp-users');
$users = new \App\Users($db);


$routes = new RouteCollector(new Std(), new GroupCountBased());

$routes->get('/users', new \App\Controller\ListUsers($users));
$routes->post('/users', new \App\Controller\CreateUser($users));
$routes->get('/users/{id}', new \App\Controller\ViewUser($users));
$routes->put('/users/{id}', new \App\Controller\UpdateUser($users));
$routes->delete('/users/{id}', new \App\Controller\DeleteUser($users));

$server = new Server(new \App\Router($routes));

$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);

$server->listen($socket);

$server->on('error', function (Exception $exception) {
    echo $exception->getMessage() . PHP_EOL;
});

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . "\n";

$loop->run();
