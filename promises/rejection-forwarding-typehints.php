<?php

require '../vendor/autoload.php';

$deferred = new \React\Promise\Deferred();

$deferred->promise()
    ->otherwise(function($data){
        echo $data . PHP_EOL;

        throw new InvalidArgumentException('some ' . $data);
    })
    ->otherwise(function(InvalidArgumentException $e){          // <-- This handler will be skipped
        $message = $e->getMessage();                            // because in the previous promise
        echo $message . PHP_EOL;                                // we have thrown a LogicException

        throw new BadFunctionCallException(strtoupper($message));
    })->otherwise(function(InvalidArgumentException $e){
        echo $e->getMessage() . PHP_EOL;
    });

$deferred->reject('error');
