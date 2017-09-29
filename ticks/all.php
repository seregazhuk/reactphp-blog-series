<?php

require '../vendor/autoload.php';

$eventLoop = \React\EventLoop\Factory::create();

$eventLoop->addTimer(0, function(){
    echo "Timer\n";
});

$eventLoop->futureTick(function(){
    echo "Future tick\n";
});

$writable = new \React\Stream\WritableResourceStream(fopen('php://stdout', 'w'), $eventLoop);
$writable->write("I\O");

$eventLoop->run();
