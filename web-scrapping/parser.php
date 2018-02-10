<?php

require '../vendor/autoload.php';

use Clue\React\Buzz\Browser;
use Psr\Http\Message\ResponseInterface;
use React\Promise\Promise;
use React\Promise\PromiseInterface;
use Symfony\Component\DomCrawler\Crawler;

$loop = React\EventLoop\Factory::create();

class Parser {
    const BASE_URL = 'http://www.imdb.com';
    const TIMEOUT = 5;

    private $parsed = [];

    /**
     * @var PromiseInterface[]
     */
    private $requests = [];

    /**
     * @var Browser
     */
    private $browser;
    /**
     * @var \React\EventLoop\LoopInterface
     */
    private $loop;

    public function __construct(Browser $browser, \React\EventLoop\LoopInterface $loop)
    {
        $this->browser = $browser->withBase(self::BASE_URL);
        $this->loop = $loop;
    }

    public function parse($url)
    {
        $this->makeRequest($url, function(ResponseInterface $response) {
            $crawler = new Crawler((string)$response->getBody());
            $monthLinks = $crawler->filter('.date_select option')->extract(['value']);
            foreach ($monthLinks as $monthLink) {
                $this->parseMonthPage($monthLink);
            }
        });
    }

    private function parseMonthPage($monthPageUrl)
    {
        $this->makeRequest($monthPageUrl, function(ResponseInterface $response) {
            $crawler = new Crawler((string)$response->getBody());
            $movieLinks = $crawler->filter('.overview-top h4 a')->extract(['href']);

            foreach ($movieLinks as $movieLink) {
                $this->parseMovieData($movieLink);
            }
        });
    }

    private function parseMovieData($moviePageUrl)
    {
        $this->makeRequest($moviePageUrl, function(ResponseInterface $response) {
            $crawler = new Crawler((string)$response->getBody());
            $title = trim($crawler->filter('h1')->text());

            $genres = $crawler->filter('[itemprop="genre"] a')->extract(['_text']);
            $description = trim($crawler->filter('[itemprop="description"]')->text());

            $crawler->filter('#titleDetails .txt-block')->each(function (Crawler $crawler) {
                foreach ($crawler->children() as $node) {
                    $node->parentNode->removeChild($node);
                }
            });
            $releaseDate = trim($crawler->filter('#titleDetails .txt-block')->eq(2)->text());

            echo $title . PHP_EOL;
            $this->parsed[] = [
                'title' => $title,
                'genres' => $genres,
                'description' => $description,
                'release_date' => $releaseDate,
            ];
        });
    }

    /**
     * @param string $url
     * @param callable $callback
     */
    private function makeRequest($url, callable $callback)
    {
        /** @var Promise $promise */
        $promise = $this->browser->get($url)
            ->then(function(ResponseInterface $response) use ($callback, $url){
                $callback($response);
                //$this->clearRequest($url);
            }, function(Exception $exception) use ($url) {
                echo $exception->getMessage() . PHP_EOL;
                //$this->clearRequest($url);
            })->always(function() use ($url){
                $this->clearRequest($url);
            });

        $this->requests[$url] = $promise;
        $this->loop->addTimer(self::TIMEOUT, function () use($promise) {
            $promise->cancel();
        });
    }

    private function clearRequest($url)
    {
        unset($this->requests[$url]);
        echo count($this->requests) . PHP_EOL;
    }

    public function getMovies()
    {
        return $this->parsed;
    }
}

$parser = new Parser(new Browser($loop), $loop);
$parser->parse('movies-coming-soon');

$loop->run();

echo 'After the loop' . PHP_EOL;
print_r($parser->getMovies());