<?php

require '../vendor/autoload.php';

use React\EventLoop\Timer\TimerInterface;

$loop = React\EventLoop\Factory::create();
$deferred = new React\Promise\Deferred();

$promise = $deferred->promise();
$promise->progress(function($data){
    echo 'Progress: ' . $data . PHP_EOL;
});
$promise->done(function($data){
    echo 'Done: ' . $data . PHP_EOL;
});

$progress = 1;
$loop->addPeriodicTimer(1, function(TimerInterface $timer) use ($deferred, &$progress){
    $deferred->notify($progress++);
    if($progress > 10) {
        $timer->cancel();
        $deferred->resolve('Finished');
    }
});

$loop->run();