<?php

require __DIR__ . '/../vendor/autoload.php';

use React\Http\Server;
use React\Http\Response;
use React\EventLoop\Factory;
use Psr\Http\Message\ServerRequestInterface;
use React\Stream\ReadableStreamInterface;

$loop = Factory::create();
$filesystem = \React\Filesystem\Filesystem::create($loop);

$server = new Server(
    function (ServerRequestInterface $request) use ($filesystem) {
        $params = $request->getQueryParams();
        $fileName = $params['video'] ?? null;

        if ($fileName === null) {
            return new Response(200, ['Content-Type' => 'text/plain'], 'Video streaming server');
        }

        $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . $fileName;
        $file = $filesystem->file($filePath);

        return $file->open('r')->then(
            function (ReadableStreamInterface $stream) {
                return new Response(200, ['Content-Type' => 'video/mp4'], $stream);
            }
        );
    }
);

$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . "\n";

$loop->run();

