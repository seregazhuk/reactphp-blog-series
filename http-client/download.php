<?php

require __DIR__ . '/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$client = new React\HttpClient\Client($loop);
$file = new \React\Stream\WritableResourceStream(fopen('sample.mp4', 'w'), $loop);

$request = $client->request('GET', 'http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_1mb.mp4');

$request->on('response', function (\React\HttpClient\Response $response) use ($file) {
    $size = $response->getHeaders()['Content-Length'];
    $currentSize = 0;

    $through = new \React\Stream\ThroughStream();
    $through->on('data', function($data) use ($size, &$currentSize){
        $currentSize += strlen($data);
        echo "\033[1A", "Downloading: ", number_format($currentSize / $size * 100), "%\n";
    });

    $response->pipe($through)->pipe($file);
});

$request->end();
echo "\n";
$loop->run();