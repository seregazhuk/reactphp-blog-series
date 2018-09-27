<?php

require __DIR__ . '/../vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();
$stream = new \React\Stream\ReadableResourceStream(fopen('file.txt', 'r'), $loop, 1);

$stream->on('data', function($data) use ($stream, $loop){
    echo $data, "\n";
    $stream->pause();

    $loop->addTimer(1, function() use ($stream) {
        $stream->resume();
    });
});

$loop->run();
