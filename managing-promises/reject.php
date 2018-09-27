<?php

require __DIR__ . '/../vendor/autoload.php';

$deferred = new \React\Promise\Deferred();
$deferred->resolve('my-value');

$promise = React\Promise\reject($deferred->promise());
$promise->then(null, function($reason){
    echo 'Promise was rejected with: ' . $reason . PHP_EOL;
});
