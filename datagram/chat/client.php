<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$factory = new React\Datagram\Factory($loop);

$stdin = new \React\Stream\ReadableResourceStream(STDIN, $loop);

$factory->createClient('localhost:1234')
    ->then(
        function (React\Datagram\Socket $client) use ($stdin, $loop) {
            $name = '';
            echo "Enter your name: ";

            $client->on('message', function($message){
                echo $message . "\n";
            });

            $client->on('close', function() use ($loop) {
                $loop->stop();
            });

            $stdin->on('data', function($data) use ($client, &$name, $loop) {
                $data = trim($data);

                if(empty($name)) {
                    $name = $data;
                    $client->send(json_encode(['message' => '', 'name' => $name, 'type' => 'enter']));
                    return;
                }

                if($data == ':exit') {
                    $client->send(json_encode(['name' => $name, 'message' => '', 'type' => 'leave']));
                    $client->end();
                }

                $message = json_encode(['message' => $data, 'name' => $name, 'type' => 'message']);
                $client->send($message);
            });
        },
        function(Exception $error) {
            echo "ERROR: {$error->getMessage()}\n";
        });

$loop->run();