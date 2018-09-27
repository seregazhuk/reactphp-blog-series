<?php

require __DIR__ . '/../vendor/autoload.php';

$firstResolver = new \React\Promise\Deferred();
$secondResolver = new \React\Promise\Deferred();

$pending = [
    $firstResolver->promise(),
    $secondResolver->promise()
];

$promise = \React\Promise\race($pending)
    ->then(
        function($resolved){
            echo 'resolved with' . $resolved . PHP_EOL;
        },
        function($failed){
            echo 'failed with' . $failed;
    });


$loop = \React\EventLoop\Factory::create();
$loop->addTimer(2, function() use ($firstResolver){
    $firstResolver->resolve(10);
});
$loop->addTimer(1, function () use ($secondResolver) {
    $secondResolver->reject(20);
});

$loop->run();
