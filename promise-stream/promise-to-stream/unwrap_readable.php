<?php

require '../../vendor/autoload.php';

$deferred = new \React\Promise\Deferred();
$stream = \React\Promise\Stream\unwrapReadable($deferred->promise());

$stream->on('data', function($data) {
    echo 'Received: ' . $data . PHP_EOL;
});

$loop = \React\EventLoop\Factory::create();
$deferred->resolve(new \React\Stream\ReadableResourceStream(fopen('php://stdin', 'r'), $loop));

$loop->run();
