<?php

require __DIR__ . '/../vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();
$stream = new \React\Stream\ReadableResourceStream(fopen('file.txt', 'r'), $loop);

$stream->on('data', function($data){
    // process data *line by line*
});

$stream->on('end', function(){
    echo "finished\n";
});

$loop->run();
