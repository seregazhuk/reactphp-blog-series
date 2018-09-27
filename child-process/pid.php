<?php

require __DIR__ . '/../vendor/autoload.php';

use React\EventLoop\Factory;
use React\ChildProcess\Process;

$loop = Factory::create();
$process = new Process('ping 8.8.8.8');

$process->start($loop);
$process->stdout->on('data', function($data) {
    echo $data;
});

$process->on('exit', function($exitCode, $termSignal) use ($loop) {
    echo "Process exited with signal: $termSignal";
    $loop->stop();
});

$loop->addTimer(3, function() use ($process, $loop){
    $pid = $process->getPid();
    echo "Sending KILL signal to PID: $pid\n";
    (new Process("kill {$pid}"))->start($loop);
});
$loop->run();

