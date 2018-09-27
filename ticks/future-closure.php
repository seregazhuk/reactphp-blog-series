<?php

require __DIR__ . '/../vendor/autoload.php';

$eventLoop = \React\EventLoop\Factory::create();

$string = "Tick!\n";
$eventLoop->futureTick(function() use($string) {
    echo $string;
});

echo "Loop starts\n";

$eventLoop->run();

echo "Loop stops\n";
