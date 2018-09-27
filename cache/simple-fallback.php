<?php

require __DIR__ . '/../vendor/autoload.php';

$cache = new React\Cache\ArrayCache();
$cache->set('foo', 'bar');

$data = null;

$cache->get('baz')
    ->then(function($value) use (&$data) {
        $data = $value;
    },function() use (&$data) {
        $data = 'default';
    });

echo $data;

