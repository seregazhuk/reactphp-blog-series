<?php

use React\EventLoop\Factory;
use React\Filesystem\Filesystem;

require __DIR__ . '/../../vendor/autoload.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);

$filesystem->getAdapter()
    ->unlink('test_link.txt')
    ->then(function($path){
        echo $path . PHP_EOL;
    });

$loop->run();
