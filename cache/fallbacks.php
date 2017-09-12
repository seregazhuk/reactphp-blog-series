<?php

require '../vendor/autoload.php';

$cache = new React\Cache\ArrayCache();
$cache->set('foo', 'bar');

$getFromDatabase = function() {
    $resolve = function(callable $resolve, callable $reject) {
        return $resolve('some data from database');
    };
    return new React\Promise\Promise($resolve);
};

$data = null;

$cache->get('baz')
    ->then(function($value) use (&$data) {
        $data = $value;
    }, $getFromDatabase)
    ->then(function($value) use (&$data) {
        $data = $value;
    });

$cache->remove('s');

echo $data;

