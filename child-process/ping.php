<?php

require '../vendor/autoload.php';

use React\EventLoop\Factory;
use React\ChildProcess\Process;

$loop = Factory::create();
$process = new Process('ping 8.8.8.8');

$process->start($loop);
$process->stdout->on('data', function($data){
    echo $data;
});

$process->on('exit', function($exitCode, $termSignal) {
    echo 'Process exited with code ' . $exitCode . PHP_EOL;
});

$loop->run();
