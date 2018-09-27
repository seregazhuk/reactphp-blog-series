<?php

require __DIR__ . '/../vendor/autoload.php';

$deferred = new \React\Promise\Deferred();
$promise = React\Promise\resolve($deferred->promise());
var_dump($deferred->promise() === $promise);

$deferred->resolve('hello world');

$promise = React\Promise\resolve($value = 'my-value');
$promise->then(function ($value) {
    echo $value . PHP_EOL;
});
