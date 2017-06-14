<?php

require '../vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();
$stream = new \React\Stream\ReadableResourceStream(fopen('file.txt', 'r'), $loop);

echo "Open\n";
var_dump($stream->isReadable());

$stream->on('data', function($data){
    // process received data
});

$stream->on('end', function() use ($stream){
    echo "End\n";
    var_dump($stream->isReadable());
});

$stream->on('close', function() use ($stream){
    echo "Close\n";
    var_dump($stream->isReadable());
});

$loop->run();