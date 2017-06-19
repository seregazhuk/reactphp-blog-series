<?php

require '../vendor/autoload.php';

use React\Socket\ConnectionInterface;

$loop = React\EventLoop\Factory::create();
$connector = new React\Socket\Connector($loop);

$connector
    ->connect('127.0.0.1:8080')
    ->then(function (ConnectionInterface $conn) use ($loop) {
        $conn->pipe(new React\Stream\WritableResourceStream(STDOUT, $loop));
        $conn->write("Hello World!\n");
});

$loop->run();