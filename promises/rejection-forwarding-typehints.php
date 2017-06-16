<?php

require '../vendor/autoload.php';

$deferred = new \React\Promise\Deferred();

$deferred->promise()
    ->otherwise(function($data){
        echo $data . PHP_EOL;

        throw new InvalidArgumentException('some ' . $data);
    })
    ->otherwise(function(InvalidArgumentException $e){
        $message = $e->getMessage();
        echo $message . PHP_EOL;

        throw new BadFunctionCallException(strtoupper($message));
    })
    ->otherwise(function(InvalidArgumentException $e){   // <-- This handler will be skipped
        echo $e->getMessage() . PHP_EOL;                 // because in the previous promise
    });                                                  // we have thrown a LogicException

$deferred->reject('error');
