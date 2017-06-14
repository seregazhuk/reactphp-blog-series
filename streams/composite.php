<?php

require '../vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();

$stdin = new \React\Stream\ReadableResourceStream(STDIN, $loop);
$stdout = new \React\Stream\WritableResourceStream(STDOUT, $loop);
$composite = new \React\Stream\CompositeStream($stdin, $stdout);

$composite->on('data', function ($chunk) use ($composite) {
    $composite->write('You said: ' . $chunk);
});

$loop->run();