<?php

use React\EventLoop\LoopInterface;
use React\Stream\ReadableResourceStream;

require '../../vendor/autoload.php';;

function getDataFromFile($path, LoopInterface $loop) {
    $stream = new ReadableResourceStream(fopen($path, 'r'), $loop);
    return \React\Promise\Stream\buffer($stream);
}


function processData($data) {

}

$loop = \React\EventLoop\Factory::create();
getDataFromFile('file.txt', $loop)
    ->then('trim')
    ->then(function($string) {
        return str_replace(' ', '-', $string);
    })
    ->then('strtolower')
    ->then('var_dump')
    ->then(function() {
        echo 'Done' . PHP_EOL;
});

$loop->run();
