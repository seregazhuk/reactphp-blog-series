<?php

use React\EventLoop\Factory;
use React\Filesystem\Filesystem;

require __DIR__ . '/../../vendor/autoload.php';

$loop = Factory::create();
$filesystem = Filesystem::create($loop);

$filesystem->file('test.txt')->chown('serega')->then(function(){
    echo 'Owner changed' . PHP_EOL;
}, function(Exception $e){
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
});

$loop->run();
