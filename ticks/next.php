<?php

use React\EventLoop\LoopInterface;

require __DIR__ . '/../vendor/autoload.php';

$eventLoop = \React\EventLoop\Factory::create();
$count = 0;
$callback = function (LoopInterface $eventLoop) use (&$callback, &$count) {
    echo "Hello world\n";
    if($count > 20) $eventLoop->stop();
    $eventLoop->nextTick($callback);
};

$eventLoop->nextTick($callback);
$eventLoop->nextTick(function(LoopInterface $eventLoop){
    $eventLoop->stop();
});
$eventLoop->run();
echo "Finished\n";
