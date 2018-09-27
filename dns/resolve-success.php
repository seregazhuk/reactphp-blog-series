<?php

require __DIR__ . '/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$factory = new React\Dns\Resolver\Factory();

$dns = $factory->create('8.8.8.8', $loop);
$dns->resolve('google.com')
    ->then(function ($ip) {
        echo "google.com: $ip\n";
    });

$loop->run();
