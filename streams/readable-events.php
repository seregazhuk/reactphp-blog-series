<?php

require '../vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();
$stream = new \React\Stream\ReadableResourceStream(fopen('file.txt', 'r'), $loop);

$stream->on('data', function($data){
   echo $data, "\n";
});

$stream->on('end', function(){
    echo "Finished\n";
});

$stream->on('close', function(){
    echo "The stream was closed\n";
});

$loop->run();