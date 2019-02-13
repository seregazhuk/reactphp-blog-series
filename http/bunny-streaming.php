<?php

require __DIR__ . '/../vendor/autoload.php';

use React\Http\Server;
use React\Http\Response;
use React\EventLoop\Factory;
use Psr\Http\Message\ServerRequestInterface;

$loop = Factory::create();

$filesystem = \React\Filesystem\Filesystem::create($loop);

$server = new Server(function (ServerRequestInterface $request) use ($filesystem) {
    $file = $filesystem->file('media/bunny.mp4');

    return $file->open('r')->then(
        function (\React\Filesystem\Stream\ReadableStream $stream) {
            return new Response(200, ['Content-Type' => 'video/mp4'], $stream);
        },
        function (Exception $exception) {
            echo $exception->getMessage() . PHP_EOL;
        }
    );
});

$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . "\n";

$loop->run();
