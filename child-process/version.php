<?php

require '../vendor/autoload.php';

use React\EventLoop\Factory;
use React\ChildProcess\Process;

$loop = Factory::create();
$process = new Process('php -v');

$process->start($loop);
$process->stdout->on('data', function($data){
    echo $data, "\n";
});

$process->on('exit', function($exitCode, $termSignal) {
    echo 'Process exited with code ' . $exitCode . PHP_EOL;
});


$loop->run();

