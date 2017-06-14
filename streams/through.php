<?php

require '../vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();

$readable = new \React\Stream\ReadableResourceStream(fopen('file.txt', 'r+'), $loop, 1);
$output = new \React\Stream\WritableResourceStream(fopen('php://stdout', 'w'), $loop);

$through = new \React\Stream\ThroughStream('strtoupper');
$readable->pipe($through)->pipe($output);

$loop->run();