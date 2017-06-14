<?php

require '../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

$asyncPromise = new React\Promise\Promise(function($resolve) use ($loop) {
    $loop->nextTick(function() use($resolve) {
        $resolve('Async Promise');
    });
});

$syncPromise = new React\Promise\Promise(function($resolve) {
    $resolve('Sync Promise');
});

$asyncPromise->then(function($value) {
    echo $value.PHP_EOL;
});

$syncPromise->then(function($value) {
    echo $value.PHP_EOL;
});

$loop->run();