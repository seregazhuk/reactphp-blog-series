<?php

require __DIR__ . '/../../vendor/autoload.php';

$deferred = new \React\Promise\Deferred();
$stream = \React\Promise\Stream\unwrapReadable($deferred->promise());

$stream->on('close', function() {
    echo 'Closed';
});

$loop = \React\EventLoop\Factory::create();
$stdin = new \React\Stream\ReadableResourceStream(fopen('php://stdin', 'r'), $loop);
$deferred->resolve($stdin);
$stdin->close();

$loop->run();
