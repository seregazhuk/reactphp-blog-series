<?php

use React\EventLoop\Factory;
use React\Filesystem\Filesystem;
use React\Filesystem\Node\FileInterface;

require __DIR__ . '/../../vendor/autoload.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);

$filesystem->file('new.txt')->time()->then(function($time){
    print_r($time);
});

$loop->run();
