<?php

require '../vendor/autoload.php';

use function Clue\React\Block\await;

$loop = React\EventLoop\Factory::create();

$resolve = function(callable $resolve, callable $reject) use ($loop) {
    $loop->addTimer(5, function() use ($resolve) {
        return $resolve('Hello wolrd!');
    });
};

$cancel = function(callable $resolve, callable $reject) {
    $reject(new \Exception('Promise cancelled!'));
};

$promise = new React\Promise\Promise($resolve, $cancel);

try {
    $value = await($promise, $loop);
    // promise successfully fulfilled with $value
    echo 'Result: ' . $value;
} catch (\Exception $exception) {
    // promise rejected with $exception
    echo 'ERROR: ' . $exception->getMessage();
}
