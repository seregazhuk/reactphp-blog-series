<?php

require __DIR__ . '/../vendor/autoload.php';

use React\EventLoop\LoopInterface;
use React\HttpClient\Client;
use React\HttpClient\Request;
use React\HttpClient\Response;
use React\Stream\WritableResourceStream;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

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
     * @var ConsoleOutput
     */
    protected $output;

    /**
     * @var ProgressBar
     */
    protected $bar;

    /**
     * @var array
     */
    protected $files = [];

    /**
     * @param Client $client
     * @param LoopInterface $loop
     * @param ConsoleOutput $output
     */
    public function __construct(Client $client, LoopInterface $loop, ConsoleOutput $output)
    {
        $this->client = $client;
        $this->loop = $loop;
        $this->output = $output;
    }

    /**
     * @param array $files
     * @param int $limit
     */
    public function download(array $files, $limit = 0)
    {
        $this->files = $files;
        $this->bar = new ProgressBar($this->output, count($this->files));
        $this->bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% Time left: %estimated:-6s%');
        $this->bar->start();

        $max = $limit ?: count($this->files);
        while($max --) {
            $this->runDownload();
        }

        $this->loop->run();
    }

    /**
     * @param string $url
     * @return Request
     */
    public function initRequest($url)
    {
        $request = $this->client->request('GET', $url);
        $fileName = basename($url);

        $file = new WritableResourceStream(fopen($fileName, 'w'), $this->loop);
        $request->on('response', function (Response $response) use ($file) {
            $response->pipe($file);

            $response->on('end', function () {
                $this->bar->advance();
                if ($this->files) {
                    $this->runDownload();
                }
            });
        });

        return $request;
    }

    protected function runDownload()
    {
        $file = array_pop($this->files);
        $request = $this->initRequest($file);
        $request->end();
    }
}

$loop = React\EventLoop\Factory::create();
$client = new React\HttpClient\Client($loop);

$files = [
    'http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_1mb.mp4',
    'http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_10mb.mp4',
    'http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_20mb.mp4',
    'http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_30mb.mp4',
    'http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_40mb.mp4',
    'http://www.sample-videos.com/video/mp4/720/big_buck_bunny_720p_50mb.mp4',
];

(new Downloader($client, $loop, new ConsoleOutput()))->download($files, 2);

