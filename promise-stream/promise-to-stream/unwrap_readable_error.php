<?php

require __DIR__ . '/../../vendor/autoload.php';

$deferred = new \React\Promise\Deferred();
$stream = \React\Promise\Stream\unwrapReadable($deferred->promise());

$stream->on('data', function($data) {
    echo 'Received: ' . $data . PHP_EOL;
});

$stream->on('error', function(Exception $error) {
    echo 'Error: ' . $error->getMessage() . PHP_EOL;
});

$deferred->resolve('Hello!');

