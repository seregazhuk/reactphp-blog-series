<?php

require __DIR__ . '/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

$resolve = function(callable $resolve, callable $reject) use ($loop) {
    $loop->addTimer(5, function() use ($resolve) {
        return $resolve('Hello wolrd!');
    });
};

$cancel = function(callable $resolve, callable $reject) {
    $reject(new \Exception('Promise cancelled!'));
};

$promise = new React\Promise\Promise($resolve, $cancel);

$loop->run();
