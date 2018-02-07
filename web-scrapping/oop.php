<?php

require '../vendor/autoload.php';

use Clue\React\Buzz\Browser;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler;

$loop = React\EventLoop\Factory::create();

class Parser {
    const BASE_URL = 'http://www.imdb.com';

    /**
     * @var Browser
     */
    private $browser;

    /**
     * @var
     */
    private $movies = [];

    public function __construct(Browser $browser)
    {
        $this->browser = $browser->withBase(self::BASE_URL);
    }

    public function parse($url)
    {
        $this->browser->get($url)
            ->then(function(ResponseInterface $response) {
                $crawler = new Crawler((string)$response->getBody());
                $monthLinks = $crawler->filter('.date_select option')->extract(['value']);
                foreach ($monthLinks as $monthLink) {
                    $this->getLinksFromMonthPage($monthLink);
                }
            }, function(Exception $e){
                echo $e->getMessage();
            });
    }

    private function getLinksFromMonthPage($monthPageUrl)
    {
        $this->browser->get($monthPageUrl)
            ->then(function(ResponseInterface $response) {
                $crawler = new Crawler((string)$response->getBody());
                $movieLinks = $crawler->filter('.overview-top h4 a')->extract(['href']);

                foreach ($movieLinks as $movieLink) {
                    $this->saveMovieData($movieLink);
                }
            });
    }

    private function saveMovieData($moviePageUrl)
    {
        $this->browser->get($moviePageUrl)
            ->then(function(ResponseInterface $response){
                $crawler = new Crawler((string)$response->getBody());
                $title = $crawler->filter('h1')->text();
                echo $title . PHP_EOL;
        });
    }
}


$parser = new Parser(new Browser($loop));
$parser->parse('movies-coming-soon');

$loop->run();
