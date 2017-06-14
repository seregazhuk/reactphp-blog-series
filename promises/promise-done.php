<?php

require '../vendor/autoload.php';

$deferred = new React\Promise\Deferred();

$promise = $deferred->promise();
$promise->done(function($data){
    echo 'Done: ' . $data . PHP_EOL;
});

$deferred->resolve('hello world');
