<?php

use React\EventLoop\Factory;
use React\Filesystem\Filesystem;
use React\Filesystem\Node\FileInterface;

require __DIR__ . '/../../vendor/autoload.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);

$file = $filesystem->file('test.txt')->rename('new.txt')->then(function(FileInterface $file){
    echo 'File was renamed to: ' . $file->getPath() . PHP_EOL;
});

$loop->run();
