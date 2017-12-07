<?php

use React\EventLoop\LoopInterface;
use React\Stream\ReadableResourceStream;

require '../../vendor/autoload.php';

class Processor {
    public function process($data)
    {
        echo $data . PHP_EOL;
        echo 'Done' . PHP_EOL;
    }
}

class Provider {
    public function get($path, LoopInterface $loop)
    {
        $spool = "";

        $stream = new ReadableResourceStream(
            fopen($path, 'r'), $loop
        );

        $stream->on('data', function($data) use (&$spool) {
            $spool .= $data;
        });

        $stream->on('end', function() use (&$spool) {
            echo $spool;
            // ???
        });
    }
}

$loop = \React\EventLoop\Factory::create();

(new Provider())->get('file.txt', $loop);

$loop->run();
