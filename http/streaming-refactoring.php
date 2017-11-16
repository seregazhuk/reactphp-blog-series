<?php

require '../vendor/autoload.php';

use React\Http\Server;
use React\Http\Response;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Stream\ReadableResourceStream;
use Psr\Http\Message\ServerRequestInterface;

class VideoStreaming
{

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
        $file = $this->getFilePath($request);
        if (empty($file)) {
            return new Response(200, ['Content-Type' => 'text/plain'], 'Video streaming server');
        }

        return $this->makeResponseFromFile($file);
    }

    /**
     * @param string $filePath
     * @return Response
     */
    protected function makeResponseFromFile($filePath)
    {
        @$file = fopen($filePath, 'r');
        if (!$file) {
            return new Response(404, ['Content-Type' => 'text/plain'], "Video $filePath doesn't exist on server.");
        }

        $stream = new ReadableResourceStream($file, $this->eventLoop);

        return new Response(200, ['Content-Type' => $this->getMimeTypeByExtension($filePath)], $stream);
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    protected function getFilePath(ServerRequestInterface $request)
    {
        $file = $request->getQueryParams()['file'] ?? '';

        if (empty($file)) return '';

        return __DIR__ . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . basename($file);
    }

    /**
     * @param string $filename
     * @return string|null
     */
    protected function getMimeTypeByExtension($filename) {
        $types = [
            '.afl' => 'video/animaflex',
            '.asf' => 'video/x-ms-asf',
            '.asx' => 'video/x-ms-asf',
            '.avi' => 'video/avi',
            '.avs' => 'video/avs-video',
            '.dif' => 'video/x-dv',
            '.dl' => 'video/dl',
            '.dv' => 'video/x-dv',
            '.fli' => 'video/fli',
            '.fmf' => 'video/x-atomic3d-feature',
            '.gl' => 'video/gl',
            '.isu' => 'video/x-isvideo',
            '.m1v' => 'video/mpeg',
            '.m2a' => 'audio/mpeg',
            '.m2v' => 'video/mpeg',
            '.mjpg' => 'video/x-motion-jpeg',
            '.moov' => 'video/quicktime',
            '.mov' => 'video/quicktime',
            '.movie' => 'video/x-sgi-movie',
            '.mp2' => 'video/mpeg',
            '.mp3' => 'video/mpeg',
            '.mpa' => 'audio/mpeg',
            '.mpe' => 'video/mpeg',
            '.mpeg' => 'video/mpeg',
            '.mpg' => 'audio/mpeg',
            '.mv' => 'video/x-sgi-movie',
            '.qt' => 'video/quicktime',
            '.qtc' => 'video/x-qtc',
            '.rv' => 'video/vnd.rn-realvideo',
            '.scm' => 'video/x-scm',
            '.vdo' => 'video/vdo',
            '.viv' => 'video/vivo',
            '.vivo' => 'video/vivo',
            '.vos' => 'video/vosaic',
            '.xsr' => 'video/x-amt-showrun',
        ];

        foreach ($types as $extension => $type) {
            if(substr($filename, -strlen($extension)) === $extension) {
                return $type;
            }
        }

        return null;
    }
}

$loop = Factory::create();

$videoStreaming = new VideoStreaming($loop);
$server = new Server($videoStreaming);

$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . "\n";
$loop->run();
