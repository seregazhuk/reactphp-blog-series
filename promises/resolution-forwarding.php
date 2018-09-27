<?php

require __DIR__ . '/../vendor/autoload.php';

$deferred = new \React\Promise\Deferred();

$deferred->promise()
    ->then(function($data){
        echo $data . PHP_EOL;
        return $data . ' world';
    })
    ->then(function($data){
        echo $data . PHP_EOL;
        return strtoupper($data);
    })->then(function($data){
        echo $data . PHP_EOL;
    });

$deferred->resolve('hello');
