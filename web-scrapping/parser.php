<?php

require '../vendor/autoload.php';

use Clue\React\Buzz\Browser;
use Symfony\Component\DomCrawler\Crawler;

$loop = React\EventLoop\Factory::create();
$client = new Browser($loop);

class Parser
{
    /**
     * @var Browser
     */
    private $client;

    /**
     * @var array
     */
    private $parsed = [];

    public function __construct(Browser $client)
    {
        $this->client = $client;
    }

    public function parse(array $urls = [])
    {
        foreach ($urls as $url) {
             $this->client->get($url)->then(
                function (\Psr\Http\Message\ResponseInterface $response) {
                   $this->parsed[] = $this->extractFromHtml((string) $response->getBody());
                });
        }
    }

    public function extractFromHtml($html)
    {
        $crawler = new Crawler($html);

        $title = trim($crawler->filter('h1')->text());
        $genres = $crawler->filter('[itemprop="genre"] a')->extract(['_text']);
        $description = trim($crawler->filter('[itemprop="description"]')->text());

        $crawler->filter('#titleDetails .txt-block')->each(
            function (Crawler $crawler) {
                foreach ($crawler->children() as $node) {
                    $node->parentNode->removeChild($node);
                }
            }
        );

        $releaseDate = trim($crawler->filter('#titleDetails .txt-block')->eq(3)->text());

        return [
            'title'        => $title,
            'genres'       => $genres,
            'description'  => $description,
            'release_date' => $releaseDate,
        ];
    }

    public function getParsed()
    {
        return $this->parsed;
    }
}


$parser = new Parser($client);
$parser->parse([
    'http://www.imdb.com/title/tt1270797/',
    'http://www.imdb.com/title/tt2527336/'
]);

$loop->run();
print_r($parser->getParsed());
