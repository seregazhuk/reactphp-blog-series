<?php

use React\EventLoop\Factory;
use React\Filesystem\Filesystem;
use React\Filesystem\Node\FileInterface;

require __DIR__ . '/../../vendor/autoload.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);

$filesystem->file('new.txt')->size()->then(function($size){
    echo 'Size is: '. $size . ' bytes' . PHP_EOL;
});

$loop->run();
