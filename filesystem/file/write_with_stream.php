<?php

use React\EventLoop\Factory;
use React\Filesystem\Filesystem;

require '../vendor/autoload.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);

$file = $filesystem->file('test.txt');
$file->open('cw')->then(function($stream) {
    $stream->write("Hello world\n");
    $stream->end();
    echo "Data was written\n";
});

$loop->run();
