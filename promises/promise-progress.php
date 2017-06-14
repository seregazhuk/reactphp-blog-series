<?php

require '../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$deferred = new React\Promise\Deferred();

$promise = $deferred->promise();
$promise->progress(function($data){
    echo 'Progress: ' . $data . PHP_EOL;
});

$progress = 1;
$loop->addPeriodicTimer(1, function() use ($deferred, &$progress){
    $deferred->notify($progress++);
});

$loop->run();