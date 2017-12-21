<?php
require '../vendor/autoload.php';

use React\Http\Response;
use React\EventLoop\Factory;
use Psr\Http\Message\ServerRequestInterface;

$loop = Factory::create();

$server = new \React\Http\Server([
    function (ServerRequestInterface $request) {
        $files = $request->getUploadedFiles();
        /** @var \Psr\Http\Message\UploadedFileInterface|null $file */
        $file = $files['file'] ?? null;
        return new Response(200, ['Content-Type' => 'text/plain'],  $file ? $file->getClientFilename() : '');
    }
]);

$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;

$loop->run();
