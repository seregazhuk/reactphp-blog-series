<?php

use React\EventLoop\Factory;
use React\Filesystem\Filesystem;

require '../vendor/autoload.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);

$filesystem->getAdapter()
    ->unlink('dir')
    ->then(function() {
        echo 'Link removed' . PHP_EOL;
    }, function(Exception $e){
        echo $e->getMessage() . PHP_EOL;
    });


$loop->run();
