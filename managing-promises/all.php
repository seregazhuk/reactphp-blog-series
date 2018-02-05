<?php

require '../vendor/autoload.php';

$firstResolver = new \React\Promise\Deferred();
$secondResolver = new \React\Promise\Deferred();

$pending = [
    $firstResolver->promise(),
    $secondResolver->promise()
];

$promise = \React\Promise\all($pending)
    ->then(function($resolved){
        print_r($resolved);
    });

$firstResolver->resolve(10);
$secondResolver->resolve(20);
