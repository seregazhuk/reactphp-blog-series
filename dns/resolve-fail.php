<?php

require '../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$factory = new React\Dns\Resolver\Factory();

$dns = $factory->create('8.8.8.8', $loop);
$dns->resolve('some-wrong-domain')
    ->otherwise(function (\React\Dns\RecordNotFoundException $e) {
        echo "Cannot resolve: " . $e->getMessage();
    });

$loop->run();
