<?php

use React\EventLoop\Factory;
use React\Filesystem\Filesystem;
use React\Filesystem\Node\FileInterface;

require __DIR__ . '/../../vendor/autoload.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);


$filesystem->file('neew.txt')->exists()->then(function(){
    echo 'File exists'. PHP_EOL;
}, function() {
    echo 'File not found' . PHP_EOL;
});

$loop->run();
