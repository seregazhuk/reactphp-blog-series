<?php

$deferred = new React\Promise\Deferred();

$promise = $deferred->promise();
$promise->done(function($data){
    throw new Exception('error');
});

$deferred->resolve('no results');