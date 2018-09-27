<?php

require __DIR__ . '/../vendor/autoload.php';

use React\EventLoop\Factory;
use React\ChildProcess\Process;

$loop = Factory::create();
$process = new Process('ls | wc -l');

$process->start($loop);

$process->stdout->on('data', function($data){
    echo 'Total number of files and folders :' . $data;
});

$loop->run();
