<?php

use React\EventLoop\Factory;
use React\Filesystem\Filesystem;

require __DIR__ . '/../../vendor/autoload.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);
$dir = $filesystem->dir('new');

$dir->create()->then(function(){
    echo 'Created' . PHP_EOL;
}, function(Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
});

$loop->run();
