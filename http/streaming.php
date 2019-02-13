<?php

require __DIR__ . '/../vendor/autoload.php';

use React\Filesystem\Stream\ReadableStream;
use React\Http\Server;
use React\Http\Response;
use React\EventLoop\Factory;
use Psr\Http\Message\ServerRequestInterface;

$loop = Factory::create();
$filesystem = \React\Filesystem\Filesystem::create($loop);

$server = new Server(function (ServerRequestInterface $request) use ($filesystem) {
    $params = $request->getQueryParams();
    $file = $params['video'] ?? null;

    if ($file === null) {
        return new Response(200, ['Content-Type' => 'text/plain'], 'Video streaming server');
    }

    $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . basename($file);

    $file = $filesystem->file($filePath);
    return $file->exists()->then(
        function () use ($file) {
            return $file->open('r')->then(
                function (ReadableStream $stream) {
                    return new Response(200, ['Content-Type' => 'video/mp4'], $stream);
                }
            );
        }, function () {
        return new Response(404, ['Content-Type' => 'text/plain'], "This video doesn't exist on server.");
    });
});

$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . "\n";
$loop->run();
