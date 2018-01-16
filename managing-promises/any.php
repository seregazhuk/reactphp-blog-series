<?php

require '../vendor/autoload.php';

$firstResolver = new \React\Promise\Deferred();
$secondResolver = new \React\Promise\Deferred();

$pending = [
    $firstResolver->promise(),
    $secondResolver->promise()
];

$promise = \React\Promise\any($pending)->then(function($resolved){
    echo $resolved . PHP_EOL;
});

$loop = \React\EventLoop\Factory::create();
$loop->addTimer(2, function() use ($firstResolver){
    $firstResolver->resolve(10);
});
$loop->addTimer(1, function () use ($secondResolver) {
    $secondResolver->resolve(20);
});

$loop->run();
