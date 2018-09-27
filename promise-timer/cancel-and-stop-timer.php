<?php

require __DIR__ . '/../vendor/autoload.php';

use function \React\Promise\Timer\timeout;

$loop = React\EventLoop\Factory::create();

$resolve = function(callable $resolve, callable $reject) use ($loop, &$timer) {
    $timer = $loop->addTimer(5, function() use ($resolve) {
        echo "resolved\n";
    });
};

$cancel = function(callable $resolve, callable $reject) use (&$timer) {
    echo "cancelled\n";
    $timer->cancel();
};

$promise = new React\Promise\Promise($resolve, $cancel);

timeout($promise, 2, $loop);

$loop->run();
