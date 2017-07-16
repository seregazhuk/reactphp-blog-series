<?php

require '../vendor/autoload.php';

use React\Http\Server;
use React\Http\Response;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Stream\ReadableResourceStream;
use Psr\Http\Message\ServerRequestInterface;

class VideoStreaming {

    /**
     * @var LoopInterface
     */
    protected $eventLoop;

    /**
     * @param LoopInterface $eventLoop
     */
    public function __construct(LoopInterface $eventLoop)
    {
        $this->eventLoop = $eventLoop;
    }

    /**
     * @param ServerRequestInterface $request
     * @return Response
     */
    function __invoke(ServerRequestInterface $request)
    {
        $file = $this->getFileName($request);
        if(empty($file)) {
            return new Response(200, ['Content-Type' => 'text/plain'], 'Video streaming');
        }

        try {
            return $this->makeStreamResponse($file);
        } catch (RuntimeException $exception) {
            return new Response(404, ['Content-Type' => 'text/plain'], $exception->getMessage());
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    protected function getFileName(ServerRequestInterface $request)
    {
        $params = $request->getQueryParams();
        return $params['file'] ?? '';
    }

    /**
     * @param string $fileName
     * @return Response
     */
    protected function makeStreamResponse($fileName)
    {
        $filePath = $this->getVideoPath($fileName);

        if(!file_exists($filePath)) {
            throw new RuntimeException("Video $fileName doesn't exist on server.");
        }

        $stream = new ReadableResourceStream(fopen($filePath, 'r'), $this->eventLoop);
        return new Response(200, ['Content-Type' => mime_content_type($filePath)], $stream);
    }

    /**
     * @param string $fileName
     * @return string
     */
    protected function getVideoPath($fileName)
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . $fileName;
    }

}

$loop = Factory::create();

$videoStreaming = new VideoStreaming($loop);
$server = new Server($videoStreaming);

$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;
$loop->run();