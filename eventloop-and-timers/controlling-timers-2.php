<?php

require '../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$counter = 0;

$periodicTimer = $loop->addPeriodicTimer(2, function() use(&$counter) {
    $counter++;
    echo "$counter\n";
});

$loop->addTimer(5, function() use($periodicTimer, $loop) {
    $loop->cancelTimer($periodicTimer);
});

$loop->run();
