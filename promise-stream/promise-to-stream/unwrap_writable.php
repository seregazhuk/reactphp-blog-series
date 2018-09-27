<?php

require __DIR__ . '/../../vendor/autoload.php';

$deferred = new \React\Promise\Deferred();
$stream = \React\Promise\Stream\unwrapWritable($deferred->promise());

$loop = \React\EventLoop\Factory::create();
$deferred->resolve(new \React\Stream\WritableResourceStream(fopen('php://stdout', 'w'), $loop));

$stream->write('Hello world!');

$loop->run();
