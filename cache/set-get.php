<?php

require '../vendor/autoload.php';

$cache = new React\Cache\ArrayCache();
$cache->set('foo', 'bar');

$cache->get('foo')->then(function($value){
    var_dump($value);
});

