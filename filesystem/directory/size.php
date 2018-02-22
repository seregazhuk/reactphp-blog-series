<?php

use React\EventLoop\Factory;
use React\Filesystem\Filesystem;

require '../vendor/autoload.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);
$dir = $filesystem->dir(__DIR__);

$dir->sizeRecursive()->then(function($size){
    print_r($size);
});

$loop->run();
