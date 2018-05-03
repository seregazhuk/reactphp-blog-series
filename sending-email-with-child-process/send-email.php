<?php

require '../vendor/autoload.php';

use React\ChildProcess\Process;
use React\Http\Server;
use React\Http\Response;
use React\EventLoop\Factory;
use Psr\Http\Message\ServerRequestInterface;

$loop = Factory::create();


$process = new Process('php send-error.php');
$process->start($loop);

$server = new Server(function (ServerRequestInterface $request) {
    throw new Exception('Error');
    return new Response(200, ['Content-Type' => 'text/plain'],  "Hello world\n");
});

$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$server->listen($socket);
$server->on('error', function (Exception $exception) use ($loop) {
    $process = new Process("php send-error.php", null, ['error' => $exception->getMessage(), 'a' => 'dsds']);
    $process->start($loop);
});

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . "\n";

$loop->run();
