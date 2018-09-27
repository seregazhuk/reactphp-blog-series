<?php

use React\EventLoop\Factory;
use React\Filesystem\Filesystem;

require __DIR__ . '/../../vendor/autoload.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);
$dir = $filesystem->dir(__DIR__);

$dir->stat()->then(function($stat){
    print_r($stat);
});

$loop->run();
