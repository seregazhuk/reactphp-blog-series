<?php

require '../vendor/autoload.php';

use React\EventLoop\LoopInterface;
use React\Http\Server;
use React\Http\Response;
use React\EventLoop\Factory;
use Psr\Http\Message\ServerRequestInterface;

class VideoStreaming {
    /**
     * @var LoopInterface
     */
    private $eventLoop;

    public function __construct(LoopInterface $eventLoop)
    {
        $this->eventLoop = $eventLoop;
    }

    protected function getFileName(ServerRequestInterface $request)
    {
        $params = $request->getQueryParams();
        if(empty($params)) {
            return new Response(200, ['Content-Type' => 'text/plain'], 'Video streaming');
        }

        return $params['file'];
    }

    function __invoke(ServerRequestInterface $request)
    {
        $params = $request->getQueryParams();
        if(empty($params)) {
            return new Response(200, ['Content-Type' => 'text/plain'], 'Video streaming');
        }

        $file = $params['video'];
        $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . $file;

        if(!file_exists($filePath)) {
            return new Response(404, ['Content-Type' => 'text/plain'], "Video $file doesn't exist on server.");
        }

        $video = new \React\Stream\ReadableResourceStream(fopen($filePath, 'r'), $this->eventLoop);

        return new Response(200, ['Content-Type' => mime_content_type($filePath)], $video);
    }

}

$loop = Factory::create();

$server = new Server(function (ServerRequestInterface $request) use ($loop) {
    $params = $request->getQueryParams();
    if(empty($params)) {
        return new Response(200, ['Content-Type' => 'text/plain'], 'Video streaming');
    }

    $file = $params['video'];
    $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . $file;

    if(!file_exists($filePath)) {
        return new Response(404, ['Content-Type' => 'text/plain'], "Video $file doesn't exist on server.");
    }

    $video = new \React\Stream\ReadableResourceStream(fopen($filePath, 'r'), $loop);

    return new Response(200, ['Content-Type' => mime_content_type($filePath)], $video);
});

$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;
$loop->run();