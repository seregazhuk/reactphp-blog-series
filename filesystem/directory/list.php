<?php

use React\EventLoop\Factory;
use React\Filesystem\Filesystem;
use React\Filesystem\Node\File;
use React\Filesystem\Node\FileInterface;

require '../vendor/autoload.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);
$dir = $filesystem->dir(__DIR__);

$dir->lsStreaming()->then(function(SplObjectStorage $nodes){
    $paths = [];
    foreach ($nodes as $node) {
        $paths[] = $node->getPath();
    }

    return $paths;
})->then(function($paths){
    print_r($paths);
});

$loop->run();
