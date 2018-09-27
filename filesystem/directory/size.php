<?php

use React\EventLoop\Factory;
use React\Filesystem\Filesystem;

require __DIR__ . '/../../vendor/autoload.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);
$dir = $filesystem->dir(__DIR__);

$dir->sizeRecursive()->then(function($size){
    echo 'Directories: ' . $size['directories'] . PHP_EOL;
    echo 'Files: ' . $size['files'] . PHP_EOL;
    echo 'Bytes: ' . $size['size'] . PHP_EOL;
});

$loop->run();
