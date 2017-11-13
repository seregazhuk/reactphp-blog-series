<?php

require '../vendor/autoload.php';

use React\Http\Server;
use React\Http\Response;
use React\EventLoop\Factory;
use Psr\Http\Message\ServerRequestInterface;

$loop = Factory::create();

$server = new Server(function (ServerRequestInterface $request) use ($loop) {
    $params = $request->getQueryParams();
    $file = $params['video'] ?? '';

    if(empty($file)) {
        return new Response(200, ['Content-Type' => 'text/plain'], 'Video streaming server');
    }

    $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . basename($file);

    @$fileStream = fopen($filePath, 'r');
    if (!$fileStream) {
        return new Response(404, ['Content-Type' => 'text/plain'], "Video $filePath doesn't exist on server.");
    }

    $video = new \React\Stream\ReadableResourceStream($fileStream, $loop);

    return new Response(200, ['Content-Type' => mime_content_type($filePath)], $video);
});

$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . "\n";
$loop->run();
