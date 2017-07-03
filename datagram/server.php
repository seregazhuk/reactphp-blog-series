<?php

require_once __DIR__.'/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$factory = new React\Datagram\Factory($loop);
$address = 'localhost:1234';

$factory->createServer($address)
    ->then(
        function (React\Datagram\Socket $server) {
            $server->on('message', function ($message, $address, $server) {
                $server->send($address . ' echo: ' . $message, $address);
                echo 'client ' . $address . ': ' . $message . PHP_EOL;
            });
        },
        function($error) {
            echo "ERROR: {$error->getMessage()}\n";
        });

echo "Listening on $address\n";
$loop->run();