<?php

require __DIR__ . '/../vendor/autoload.php';

$cache = new React\Cache\ArrayCache();
$cache->set('foo', 'bar');

$cache->get('foo')->done(function($value){
    var_dump($value);
});

