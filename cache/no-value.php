<?php

require '../vendor/autoload.php';

$cache = new React\Cache\ArrayCache();
$cache->set('foo', 'bar');

$cache->get('baz')->otherwise(function(){
    echo "There is no value in cache";
});

