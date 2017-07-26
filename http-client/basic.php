<?php

require __DIR__ . '/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$client = new React\HttpClient\Client($loop);

$request = $client->request('GET', 'http://php.net/');
$request->on('response', function (\React\HttpClient\Response $response) {
    $response->on('data', function ($chunk) {
        echo $chunk;
    });
});
$request->on('error', function (\Exception $e) {
    echo $e;
});
$request->end();
$loop->run();