<?php

require __DIR__ . '/../vendor/autoload.php';

use React\Promise\Promise;
use Recoil\React\ReactKernel;

function fakeResponse(string $url, callable $callback)
{
    $callback("response for $url");
}

function makeRequest(string $url)
{
    return new Promise(
        function (callable $resolve) use ($url) {
            fakeResponse($url, $resolve);
        }
    );
}

ReactKernel::start(
    function () {
        $promise1 = makeRequest('url1');
        $promise2 = makeRequest('url2');
        $promise3 = makeRequest('url3');

        var_dump(yield $promise1);
        var_dump(yield $promise2);
        var_dump(yield $promise3);
    }
);



