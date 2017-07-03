<?php

require_once __DIR__.'/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$factory = new React\Datagram\Factory($loop);

$stdin = new \React\Stream\ReadableResourceStream(STDIN, $loop);

$factory->createClient('localhost:1234')
    ->then(
        function (React\Datagram\Socket $client) use ($stdin) {
            $stdin->on('data', function($data) use ($client) {
                $client->send(trim($data));
            });
        },
        function($error) {
            echo "ERROR: {$error->getMessage()}\n";
        });

$loop->run();