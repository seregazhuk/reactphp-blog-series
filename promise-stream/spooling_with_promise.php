<?php

use React\EventLoop\LoopInterface;
use React\Promise\PromiseInterface;
use React\Stream\ReadableResourceStream;

require '../vendor/autoload.php';

class Processor {
    /**
     * @param PromiseInterface $promise
     * @return PromiseInterface
     */
    public function process(PromiseInterface $promise)
    {
        return $promise->then('trim')
            ->then(function($string) {
                return str_replace(' ', '-', $string);
            })
            ->then('strtolower');
    }
}

class Provider {
    /**
     * @param string $path
     * @param LoopInterface $loop
     * @return PromiseInterface
     */
    public function get($path, LoopInterface $loop)
    {
        $stream = new ReadableResourceStream(
            fopen($path, 'r'), $loop
        );
        return \React\Promise\Stream\buffer($stream);
    }
}

$loop = \React\EventLoop\Factory::create();

$processor = new Processor();
$provider = new Provider();

$processor->process($provider->get('file.txt', $loop))->then(function($data) {
    echo $data . PHP_EOL;
    echo 'Done' . PHP_EOL;
});

$loop->run();
