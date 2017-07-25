<?php

use React\EventLoop\LoopInterface;

require __DIR__ . '/../vendor/autoload.php';

class Downloader
{
    /**
     * @var \React\HttpClient\Client
     */
    protected $client;
    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var array
     */
    private $requests = [];

    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
        $this->client = new React\HttpClient\Client($this->loop);
    }

    public function download($files)
    {
        $files = is_array($files) ? $files : [$files];
        foreach ($files as $index => $file) {
            $this->initRequest($file, $index + 1);
        }

        $this->runRequests();
    }

    public function initRequest($url, $position)
    {
        $request = $this->client->request('GET', $url);
        $fileName = basename($url);
        $file = new \React\Stream\WritableResourceStream(fopen($fileName, 'w'), $this->loop);
        $request->on('response', function (\React\HttpClient\Response $response) use ($file, $fileName, $position) {
            $size = $response->getHeaders()['Content-Length'];
            $currentSize = 0;

            $through = new \React\Stream\ThroughStream();
            $through->on('data', function($data) use ($size, &$currentSize, $fileName, $position){
                $currentSize += strlen($data);
                echo str_repeat("\033[1A", $position), "$fileName: ", number_format($currentSize / $size * 100), str_repeat("%\n", $position);
            });

            $response->pipe($through)->pipe($file);
        });

        $this->requests[] = $request;
    }

    protected function runRequests()
    {
        echo str_repeat("\n", count($this->requests));

        foreach ($this->requests as $request) {
            $request->end();
        }
    }
}

$loop = React\EventLoop\Factory::create();

$files = [
    'http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_10mb.mp4',
    'http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_20mb.mp4',
    'http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_50mb.mp4',
];
(new Downloader($loop))->download($files);
$loop->run();