<?php

require '../vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();

$conn = stream_socket_client('tcp://google.com:80');
$stream = new \React\Stream\DuplexResourceStream($conn, $loop);

$stream->write('hello!');
$stream->end();

$loop->run();