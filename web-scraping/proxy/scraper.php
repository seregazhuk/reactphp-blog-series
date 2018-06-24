<?php

require '../../vendor/autoload.php';

use Clue\React\Buzz\Browser;
use React\EventLoop\LoopInterface;
use Symfony\Component\DomCrawler\Crawler;

class Scraper
{
    /**
     * @var Browser
     */
    private $client;

    /**
     * @var array
     */
    private $parsed = [];

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var LoopInterface
     */
    private $loop;

    public function __construct(Browser $client, LoopInterface $loop)
    {
        $this->client = $client;
        $this->loop = $loop;
    }

    public function scrape(array $urls = [], $timeout = 5)
    {
        $this->parsed = [];
        $this->errors = [];

        foreach ($urls as $url) {
            $promise = $this->client->get($url)->then(
                function (\Psr\Http\Message\ResponseInterface $response) {
                    $this->parsed[] = $this->extractFromHtml((string)$response->getBody());
                }, function (Exception $exception) use ($url) {
                $this->errors[$url] = $exception->getMessage();
            }
            );

            $this->loop->addTimer($timeout, function () use ($promise) {
                $promise->cancel();
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

    public function getMovieData()
    {
        return $this->parsed;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
