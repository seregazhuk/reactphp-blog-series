<?php

require '../vendor/autoload.php';

use \React\EventLoop\Timer\TimerInterface;

$loop = React\EventLoop\Factory::create();
$counter = 0;

$loop->addPeriodicTimer(2, function(TimerInterface $timer) use($loop, &$counter) {
    $counter++;
    echo "$counter ";

    if($counter == 5){
        $loop->cancelTimer($timer);
    }

    echo $timer->isActive() ?
        'Timer active' :
        'Timer detached';

    echo "\n";
});

$loop->run();
echo 'stop';