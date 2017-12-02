<?php

use React\EventLoop\LoopInterface;
use React\Stream\ReadableResourceStream;

require '../vendor/autoload.php';

function processFile($path, LoopInterface $loop) {
    $stream = new ReadableResourceStream(fopen($path, 'r'), $loop);

    return \React\Promise\Stream\buffer($stream)
        ->then('trim')
        ->then(function($string){
            return str_replace(' ', '-', $string);
        })
        ->then('strtolower')
        ->then('var_dump');
}

$loop = \React\EventLoop\Factory::create();
processFile('file.txt', $loop)->then(function(){
    echo 'Done' . PHP_EOL;
});

$loop->run();
