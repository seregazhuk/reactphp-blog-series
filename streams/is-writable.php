<?php

require __DIR__ . '/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

$readable = new \React\Stream\ReadableResourceStream(fopen('file.txt', 'r'), $loop, 1);
$output = new \React\Stream\WritableResourceStream(fopen('php://stdout', 'w'), $loop);

var_dump($output->isWritable());

$readable->on('data', function($data) use ($output){
    $output->write($data);
});

$readable->on('end', function() use ($output) {
    $output->end();
});

$loop->run();
var_dump($output->isWritable());
