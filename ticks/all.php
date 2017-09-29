<?php

require '../vendor/autoload.php';

$eventLoop = \React\EventLoop\Factory::create();

$eventLoop->addTimer(0, function(){
    echo "Timer\n";
});

$eventLoop->futureTick(function(){
    echo "Future tick\n";
});

$eventLoop->nextTick(function(){
    echo "Next tick\n";
});

$eventLoop->run();
