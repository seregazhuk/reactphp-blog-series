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

$loop->addTimer(3, function() use ($process) {
    $process->terminate();
});

$process->on('exit', function() {
    echo "Process exited\n";
});

$loop->run();
