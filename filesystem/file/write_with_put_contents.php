<?php

use React\EventLoop\Factory;
use React\Filesystem\Filesystem;

require '../vendor/autoload.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);

$file = $filesystem->file('test.txt');
$file->putContents("Hello world\n")->then(function() {
    echo "Data was written" . PHP_EOL;
});

$loop->run();
