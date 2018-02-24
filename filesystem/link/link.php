<?php

use React\EventLoop\Factory;
use React\Filesystem\Filesystem;

require '../vendor/autoload.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);

$filesystem->getAdapter()
    ->symlink('test.txt', 'test_link.txt')
    ->then(function(){
        echo 'Link created' . PHP_EOL;
    });

$loop->run();
