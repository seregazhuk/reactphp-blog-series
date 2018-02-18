<?php

use React\EventLoop\Factory;
use React\Filesystem\Filesystem;

require '../vendor/autoload.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);

$file = $filesystem->file('test.txt');
$file->open('r')
    ->then(function($stream) {
        $stream->on('data', function($chunk) {
            echo 'Chunk read' . PHP_EOL;
        });
    });

$loop->run();
