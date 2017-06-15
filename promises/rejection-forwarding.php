<?php

require '../vendor/autoload.php';

$deferred = new \React\Promise\Deferred();

$deferred->promise()
    ->otherwise(function($data){
        echo $data . PHP_EOL;
        throw new Exception('some ' . $data);
    })
    ->otherwise(function(\Exception $e){
        $message = $e->getMessage();
        echo $message . PHP_EOL;
        throw new Exception(strtoupper($message));
    })->otherwise(function(\Exception $e){
        echo $e->getMessage() . PHP_EOL;
    });

$deferred->reject('error');
