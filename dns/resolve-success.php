<?php

require '../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$factory = new React\Dns\Resolver\Factory();
$dns = $factory->create('8.8.8.8', $loop);

$dns = $factory->create('8.8.8.8', $loop);
$dns->resolve('php.net')
    ->then(function ($ip) {
        echo "php.net: $ip\n";
    });

$loop->run();
