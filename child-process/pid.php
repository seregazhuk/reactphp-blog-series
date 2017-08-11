<?php

require '../vendor/autoload.php';

use React\EventLoop\Factory;
use React\ChildProcess\Process;

$loop = Factory::create();
$process = new Process('ping 8.8.8.8');

$process->start($loop);
$process->stdout->on('data', function($data) use ($loop) {
    echo $data;
});

$loop->addTimer(5, function() use ($process, $loop){
    $pid = $process->getPid();
    echo "Sending KILL signal to PID: $pid\n";
    (new Process("kill {$pid}"))->start($loop);
});
$loop->run();

