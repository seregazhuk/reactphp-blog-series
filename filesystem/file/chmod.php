<?php

use React\EventLoop\Factory;
use React\Filesystem\Filesystem;

require '../vendor/autoload.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);

$filesystem->file('test.txt')->chmod(0755)->then(function(){
    echo 'Mode changed' . PHP_EOL;
});

$loop->run();
