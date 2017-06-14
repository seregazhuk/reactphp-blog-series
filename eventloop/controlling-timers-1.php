<?php

require '../vendor/autoload.php';

use \React\EventLoop\Timer\TimerInterface;

$loop = React\EventLoop\Factory::create();
$counter = 0;

$loop->addPeriodicTimer(2, function(TimerInterface $timer)  use(&$counter, $loop) {
    $counter++;
    echo "$counter\n";

    if($counter == 5) {
        $loop->cancelTimer($timer);
    }
});

$loop->run();
echo "Done\n";