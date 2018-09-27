<?php

use React\EventLoop\Factory;
use React\Filesystem\Filesystem;

require __DIR__ . '/../../vendor/autoload.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);

$file = $filesystem->file('test.txt');
$file->getContents()->then(function($contents) {
    echo 'Reading completed' . PHP_EOL;
});
$loop->addPeriodicTimer(1, function(){
    echo 'Timer' . PHP_EOL;
});

$loop->run();
