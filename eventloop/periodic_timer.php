<?php

require '../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$counter = 0;

$loop->addPeriodicTimer(2, function() use(&$counter) {
    $counter++;
    echo "$counter\n";
});

$loop->run();