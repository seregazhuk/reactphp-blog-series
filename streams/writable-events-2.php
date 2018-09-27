<?php

require __DIR__ . '/../vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();
$writable = new \React\Stream\WritableResourceStream(fopen('php://stdout', 'w'), $loop);

$writable->on('end', function(){
    echo "End\n"; // <-- this code will never be executed
});

$writable->on('close', function(){
    echo "Close\n";
});

$loop->addTimer(1, function() use ($writable) {
    $writable->end();
});

$loop->run();
