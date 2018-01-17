<?php

require '../vendor/autoload.php';

$firstResolver = new \React\Promise\Deferred();
$secondResolver = new \React\Promise\Deferred();
$thirdResolver = new \React\Promise\Deferred();

$pending = [
    $firstResolver->promise(),
    $secondResolver->promise(),
    $thirdResolver->promise(),
];

$promise = \React\Promise\some($pending, 2)
    ->then(function($resolved){
        echo 'Resolved' . PHP_EOL;
        print_r($resolved);
    }, function($errors){
        echo 'Failed' . PHP_EOL;
        print_r($errors);
    });

$loop = \React\EventLoop\Factory::create();
$loop->addTimer(2, function() use ($firstResolver){
    $firstResolver->resolve(10);
});
$loop->addTimer(1, function () use ($secondResolver) {
    $secondResolver->reject('second failed');
});
$thirdResolver->reject('third failed');


$loop->run();
