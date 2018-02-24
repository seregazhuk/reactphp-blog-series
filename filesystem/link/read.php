<?php

use React\EventLoop\Factory;
use React\Filesystem\Filesystem;

require '../vendor/autoload.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);

$filesystem->getAdapter()
    ->readlink('test_link.txt')
    ->then(function($path){
        echo $path . PHP_EOL;
    });

$loop->run();
