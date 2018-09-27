<?php

require __DIR__ . '/../vendor/autoload.php';

use React\Dns\Model\Message;
use React\Dns\Query\Query;
use React\Dns\Query\Executor;
use React\Dns\Protocol\Parser;
use React\Dns\Protocol\BinaryDumper;
use React\EventLoop\Factory;

$loop = Factory::create();
$executor = new Executor($loop, new Parser(), new BinaryDumper(), null);
$query = new Query('php.net', Message::TYPE_AAAA, Message::CLASS_IN, time());

$executor->query('8.8.8.8:53', $query)
    ->then(function(Message $message){
        foreach ($message->answers as $answer) {
            echo $answer->data, "\n";
        }
    });

$loop->run();
