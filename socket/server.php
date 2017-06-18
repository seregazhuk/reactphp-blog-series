<?php

require  '../vendor/autoload.php';

use React\Socket\ConnectionInterface;

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server('127.0.0.1:8080', $loop);

$socket->on('connection', function (ConnectionInterface $conn) {
    $conn->on('data', function ($data) use ($conn) {
        $conn->write(strtoupper($data));
    });
});

$loop->run();