<?php

require __DIR__ . '/../vendor/autoload.php';

$eventLoop = \React\EventLoop\Factory::create();

$callback = function () use ($eventLoop, &$callback) {
    echo "Hello world\n";
    $eventLoop->futureTick($callback);
};

$eventLoop->futureTick($callback);
$eventLoop->futureTick(function() use ($eventLoop) {
    $eventLoop->stop();
});
$eventLoop->run();
echo "Finished\n";
