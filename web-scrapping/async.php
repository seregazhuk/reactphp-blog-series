<?php

require '../vendor/autoload.php';

use Clue\React\Buzz\Browser;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\LoopInterface;
use Symfony\Component\DomCrawler\Crawler;

$loop = React\EventLoop\Factory::create();

class Parser {
    const BASE_URL = 'http://www.imdb.com';

    /**
     * @var Browser
     */
    private $browser;

    /**
     * @var LoopInterface
     */
    private $loop;

    public function __construct(Browser $browser, LoopInterface $loop)
    {
        $this->browser = $browser->withBase(self::BASE_URL);
        $this->loop = $loop;
    }

    public function parse($url)
    {
        $this->browser->get($url)
            ->then(function(ResponseInterface $response) {
                $crawler = new Crawler((string)$response->getBody());
                $monthLinks = $crawler->filter('.date_select option')->extract(['value']);
                foreach ($monthLinks as $monthLink) {
                    $this->parseMonthPage($monthLink);
                }
            }, function(Exception $e){
                echo $e->getMessage();
            });
    }

    private function parseMonthPage($monthPageUrl)
    {
        $this->browser->get($monthPageUrl)
            ->then(function(ResponseInterface $response) {
                $crawler = new Crawler((string)$response->getBody());
                $movieLinks = $crawler->filter('.overview-top h4 a')->extract(['href']);

                foreach ($movieLinks as $movieLink) {
                    $this->parseMovieData($movieLink);
                }
            }, function(Exception $e){
                echo $e->getMessage();
            });
    }

    private function parseMovieData($moviePageUrl)
    {
        $this->browser->get($moviePageUrl)
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


                $fileName = __DIR__ . '/parsed/' . $title . '.json';
                $stream = new \React\Stream\WritableResourceStream(fopen($fileName, 'w'), $this->loop);

                $stream->write(json_encode([
                    'title' => $title,
                    'genres' => $genres,
                    'description' => $description,
                    'release_date' => $releaseDate,
                ]));
                $stream->end();

        }, function(Exception $e){
            echo $e->getMessage();
        });
    }
}


$parser = new Parser(new Browser($loop), $loop);
$parser->parse('movies-coming-soon');

$loop->run();
