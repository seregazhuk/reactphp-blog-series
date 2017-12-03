<?php

use React\EventLoop\LoopInterface;
use React\Promise\PromiseInterface;
use function React\Promise\Stream\all;
use React\Stream\ReadableResourceStream;

require '../vendor/autoload.php';

class Logger {
    /**
     * @param PromiseInterface $promise
     * @return PromiseInterface
     */
    public function log(PromiseInterface $promise)
    {
        return $promise->then(function() {
            echo 'drained' . PHP_EOL;
        });
    }
}

$loop = \React\EventLoop\Factory::create();

$logger = new Logger();

$writable = new \React\Stream\WritableResourceStream(fopen('php://stdout', 'w'), $loop, 1);
$writable->write('Hello world');

React\Promise\Stream\all($writable, 'drain')->then(function() {
    echo 'drained' . PHP_EOL;
});
$writable->close();

$loop->run();
