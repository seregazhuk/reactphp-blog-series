<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use React\Promise\Promise;
use React\Promise\RejectedPromise;
use Recoil\React\ReactKernel;


function fakeResponse(string $url, callable $callback) {
    $callback("response for $url");
}

function makeRequest(string $url) {
    return new Promise(function(callable $resolve) use ($url) {
        fakeResponse($url, $resolve);
    });
}

$promise1 = makeRequest('url1');
$promise2 = makeRequest('url2');
$promise3 = makeRequest('url3');

ReactKernel::start(
    function () {
        echo 'Response 1: ', yield makeRequest('url1'), PHP_EOL;
        echo 'Response 2: ', yield makeRequest('url2'), PHP_EOL;
        echo 'Response 3: ', yield makeRequest('url3'), PHP_EOL;
    }
);

function failedOperation() {
    return new RejectedPromise(new RuntimeException('Something went wrong'));
}

ReactKernel::start(
    function () {
         try {
             yield failedOperation();
         } catch (Throwable $error) {
             echo $error->getMessage() . PHP_EOL;
         }
    }
);
