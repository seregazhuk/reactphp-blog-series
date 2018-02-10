<?php

require '../vendor/autoload.php';

use Clue\React\Buzz\Browser;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\LoopInterface;
use React\Promise\PromiseInterface;
use Symfony\Component\DomCrawler\Crawler;

$loop = React\EventLoop\Factory::create();

class Parser {
    const BASE_URL = 'http://www.imdb.com';

    /**
     * @var PromiseInterface[]
     */
    private $requests;

    /**
     * @var Browser
     */
    private $browser;

    public function __construct(Browser $browser)
    {
        $this->browser = $browser->withBase(self::BASE_URL);
    }

    public function parse($url)
    {
        $this->makeRequest($url)
            ->then(function(ResponseInterface $response) {
                $crawler = new Crawler((string)$response->getBody());
                $monthLinks = $crawler->filter('.date_select option')->extract(['value']);
                foreach ($monthLinks as $monthLink) {
                    $this->parseMonthPage($monthLink);
                }
            }, [$this, 'handleError']);
    }

    private function parseMonthPage($monthPageUrl)
    {
        $this->makeRequest($monthPageUrl)
            ->then(function(ResponseInterface $response) {
                $crawler = new Crawler((string)$response->getBody());
                $movieLinks = $crawler->filter('.overview-top h4 a')->extract(['href']);

                foreach ($movieLinks as $movieLink) {
                    $this->parseMovieData($movieLink);
                }
            }, [$this, 'handleError']);
    }

    private function parseMovieData($moviePageUrl)
    {
        $this->makeRequest($moviePageUrl)
            ->then(function(ResponseInterface $response){
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

                return [
                    'title' => $title,
                    'genres' => $genres,
                    'description' => $description,
                    'release_date' => $releaseDate,
                ];
            }, [$this, 'handleError']);
    }

    /**
     * @param string $url
     * @return PromiseInterface
     */
    private function makeRequest($url)
    {
        return $this->requests[] = $this->browser->get($url);
    }

    private function handleError(Exception $exception)
    {
        echo $exception->getMessage();
    }
}


$parser = new Parser(new Browser($loop));
$parser->parse('movies-coming-soon');

$loop->run();
