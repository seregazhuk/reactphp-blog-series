<?php

require '../vendor/autoload.php';

use function React\Promise\Timer\timeout;

$loop = React\EventLoop\Factory::create();

$promise = new React\Promise\Promise(
    function($resolve, $reject) {
        return $resolve('Hi!');
    },
    function() {
        echo "Cancelled\n";
    }
);

timeout($promise, 2.0, $loop)
    ->then(function ($value) {
        echo "resolved: " . $value, "\n";
    });
$loop->run();
