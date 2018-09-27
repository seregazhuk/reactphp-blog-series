<?php

require __DIR__ . '/../vendor/autoload.php';

use React\Stream\ReadableResourceStream;

$loop = React\EventLoop\Factory::create();
$spool = "";

$stream = new ReadableResourceStream(
    fopen('file.txt', 'r'), $loop
);

$stream->on('data', function($data) use (&$spool) {
    $spool .= $data;
});

$stream->on('end', function() use (&$spool) {
    echo $spool;
});

$loop->run();
