<?php

require __DIR__ . '/../vendor/autoload.php';

use React\Socket\ConnectionInterface;

$loop = React\EventLoop\Factory::create();
$connector = new React\Socket\Connector($loop);

$stdin = new \React\Stream\ReadableResourceStream(STDIN, $loop);
$stdout = new \React\Stream\WritableResourceStream(STDOUT, $loop);

$connector
    ->connect('127.0.0.1:8080')
    ->then(
        function (ConnectionInterface $conn) use ($stdout, $stdin) {
            $stdin->pipe($conn)->pipe($stdout);
        },
        function (Exception $exception) use ($loop){
            echo "Cannot connect to server: " . $exception->getMessage();
            $loop->stop();
        });

$loop->run();
