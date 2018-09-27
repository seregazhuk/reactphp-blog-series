<?php

require __DIR__ . '/../vendor/autoload.php';

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;
use React\Http\Server;

function getMimeTypeByExtension($filename)
{
    $types = [
        '.afl'   => 'video/animaflex',
        '.asf'   => 'video/x-ms-asf',
        '.asx'   => 'video/x-ms-asf',
        '.avi'   => 'video/avi',
        '.avs'   => 'video/avs-video',
        '.dif'   => 'video/x-dv',
        '.dl'    => 'video/dl',
        '.dv'    => 'video/x-dv',
        '.fli'   => 'video/fli',
        '.fmf'   => 'video/x-atomic3d-feature',
        '.gl'    => 'video/gl',
        '.isu'   => 'video/x-isvideo',
        '.m1v'   => 'video/mpeg',
        '.m2a'   => 'audio/mpeg',
        '.m2v'   => 'video/mpeg',
        '.mjpg'  => 'video/x-motion-jpeg',
        '.moov'  => 'video/quicktime',
        '.mov'   => 'video/quicktime',
        '.movie' => 'video/x-sgi-movie',
        '.mp2'   => 'video/mpeg',
        '.mp3'   => 'video/mpeg',
        '.mpa'   => 'audio/mpeg',
        '.mpe'   => 'video/mpeg',
        '.mpeg'  => 'video/mpeg',
        '.mpg'   => 'audio/mpeg',
        '.mp4'   => 'audio/mpeg',
        '.mv'    => 'video/x-sgi-movie',
        '.qt'    => 'video/quicktime',
        '.qtc'   => 'video/x-qtc',
        '.rv'    => 'video/vnd.rn-realvideo',
        '.scm'   => 'video/x-scm',
        '.vdo'   => 'video/vdo',
        '.viv'   => 'video/vivo',
        '.vivo'  => 'video/vivo',
        '.vos'   => 'video/vosaic',
        '.xsr'   => 'video/x-amt-showrun',
    ];

    foreach ($types as $extension => $type) {
        if (substr($filename, -strlen($extension)) === $extension) {
            return $type;
        }
    }

    return null;
}

$loop = \React\EventLoop\Factory::create();


$queryParamMiddleware = function(ServerRequestInterface $request, callable $next) {
    $params = $request->getQueryParams();

    if (!isset($params['video'])) {
        return new Response(200, ['Content-Type' => 'text/plain'], 'Video streaming server');
    }

    return $next($request);
};

$checkFileExistsMiddleware = function(ServerRequestInterface $request, callable $next) {
    $file = $request->getQueryParams()['video'];
    $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . basename($file);
    @$fileStream = fopen($filePath, 'r');

    if (!$fileStream) {
        return new Response(404, ['Content-Type' => 'text/plain'], "Video $file doesn't exist on server.");
    }

    return $next($request);
};

$videoStreamingMiddleware = function(ServerRequestInterface $request) use ($loop) {
    $file = $request->getQueryParams()['video'];
    $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . basename($file);

    $video = new \React\Stream\ReadableResourceStream(fopen($filePath, 'r'), $loop);
    return new Response(200, ['Content-Type' => getMimeTypeByExtension($filePath)], $video);
};

$server = new Server([
    $queryParamMiddleware,
    $checkFileExistsMiddleware,
    $videoStreamingMiddleware
]);

$socket = new \React\Socket\Server('127.0.0.1:8000', $loop);
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;
$loop->run();
