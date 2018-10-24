<?php

require __DIR__ . '/../vendor/autoload.php';

use React\Promise\Promise;

function fakeResponse(string $url, callable $callback) {
    $callback("response for $url");
}

function makeRequest(string $url) {
    return new Promise(function(callable $resolve) use ($url) {
        fakeResponse($url, $resolve);
    });
}

$promise1 = makeRequest('url1');
$promise2 = makeRequest('url2');
$promise3 = makeRequest('url3');

$promise1
    ->then('var_dump')
    ->then(function() use ($promise2) {
        return $promise2;
    })
    ->then('var_dump')
    ->then(function () use ($promise3) {
        return $promise3;
    })
    ->then('var_dump')
    ->then(function () {
        echo 'Complete';
    });

