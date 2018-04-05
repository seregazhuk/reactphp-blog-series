<?php

require '../vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();

$stream = new \React\Stream\ReadableResourceStream(STDIN, $loop);

$stream->on('data', function ($chunk) {
    echo $chunk . PHP_EOL;
});


$stream->on('end', function () {
    echo "Closed\n";
});


$loop->run();
