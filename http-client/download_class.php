<?php

require __DIR__ . '/../vendor/autoload.php';

use React\EventLoop\LoopInterface;
use React\HttpClient\Client;

class Downloader
{
    /**
     * @var React\EventLoop\LoopInterface;
     */
    private $loop;

    /**
     * @var \React\HttpClient\Client
     */
    protected $client;

    /**
     * @var array
     */
    private $requests = [];

    /**
     * @param Client $client
     * @param LoopInterface $loop
     */
    public function __construct(Client $client, LoopInterface $loop)
    {
        $this->client = $client;
        $this->loop = $loop;
    }

    /**
     * @param string|array $files
     */
    public function download(array $files)
    {
        foreach ($files as $index => $file) {
            $this->initRequest($file, $index + 1);
        }

        echo str_repeat("\n", count($this->requests));

        $this->runRequests();
    }

    /**
     * @param string $url
     * @param int $position
     */
    public function initRequest($url, $position)
    {
        $fileName = basename($url);
        $file = new \React\Stream\WritableResourceStream(fopen($fileName, 'w'), $this->loop);

        $request = $this->client->request('GET', $url);
        $request->on('response', function (\React\HttpClient\Response $response) use ($file, $fileName, $position) {
            $size = $response->getHeaders()['Content-Length'];
            $progress = $this->makeProgressStream($size, $fileName, $position);
            $response->pipe($progress)->pipe($file);
        });

        $this->requests[] = $request;
    }

    /**
     * @param int $size
     * @param string $fileName
     * @param int $position
     * @return \React\Stream\ThroughStream
     */
    protected function makeProgressStream($size, $fileName, $position)
    {
        $currentSize = 0;

        $progress = new \React\Stream\ThroughStream();
        $progress->on('data', function($data) use ($size, &$currentSize, $fileName, $position){
            $currentSize += strlen($data);
            echo str_repeat("\033[1A", $position), "$fileName: ", number_format($currentSize / $size * 100), "%", str_repeat("\n", $position);
        });

        return $progress;
    }

    protected function runRequests()
    {
        foreach ($this->requests as $request) {
            $request->end();
        }

        $this->requests = [];

        $this->loop->run();
    }
}

$loop = React\EventLoop\Factory::create();
$client = new React\HttpClient\Client($loop);

$files = [
    'http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_1mb.mp4',
    'http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_2mb.mp4',
    'http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_5mb.mp4',
];

(new Downloader($client, $loop))->download($files);
