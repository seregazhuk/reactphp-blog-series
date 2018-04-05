<?php

use React\EventLoop\TimerInterface;

require '../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$counter = 0;

$loop->addPeriodicTimer(2, function(TimerInterface $timer) use($loop, &$counter) {
    $counter++;
    echo "$counter ";

    if($counter == 5){
        $loop->cancelTimer($timer);
    }

    echo "\n";
});

$loop->run();
echo 'stop';
