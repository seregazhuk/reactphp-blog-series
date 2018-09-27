<?php

require __DIR__ . '/../vendor/autoload.php';

$eventLoop = \React\EventLoop\Factory::create();

$eventLoop->futureTick(function() {
    echo "Tick\n";
});

echo "Loop starts\n";

$eventLoop->run();

echo "Loop stops\n";
