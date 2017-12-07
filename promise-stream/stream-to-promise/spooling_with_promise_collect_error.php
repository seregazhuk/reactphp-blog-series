<?php

use React\EventLoop\LoopInterface;
use React\Promise\PromiseInterface;
use React\Stream\ReadableResourceStream;

require '../../vendor/autoload.php';

class Logger {
    /**
     * @param PromiseInterface $promise
     * @return PromiseInterface
     */
    public function log(PromiseInterface $promise)
    {
        return $promise->then(function(Exception $error) {
            echo 'Error ' . $error->getMessage() . PHP_EOL;
        });
    }
}

class Provider {
    /**
     * @var ReadableResourceStream
     */
    protected $stream;

    /**
     * @param string $path
     * @param LoopInterface $loop
     */
    public function __construct($path, LoopInterface $loop)
    {
        $this->stream = new ReadableResourceStream(
            fopen($path, 'r'), $loop
        );
    }

    /**
     * @return PromiseInterface
     */
    public function getData()
    {
        return \React\Promise\Stream\buffer($this->stream);
    }

    /**
     * @return PromiseInterface
     */
    public function getFirstError()
    {
        $promise = \React\Promise\Stream\first($this->stream, 'error');
        $this->stream->emit('error', [new Exception('Something went wrong')]);
        return $promise;
    }
}

$loop = \React\EventLoop\Factory::create();

$logger = new Logger();
$provider = new Provider('file.txt', $loop);

$logger->log($provider->getFirstError());

$loop->run();
