<?php


require '../vendor/autoload.php';

use Clue\React\Buzz\Browser;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler;

$loop = React\EventLoop\Factory::create();
$client = (new Browser($loop))->withBase('http://www.imdb.com');

$getLinksForMonths = function($url) use ($client) {
    $client
        ->get($url)
        ->then(function(ResponseInterface $response){

        });
};

$client->get('/movies-coming-soon/')
    ->then(function(ResponseInterface $response) use ($getLinksForMonths) {
        $crawler = new Crawler((string)$response->getBody());
        $monthLinks = $crawler->filter('.date_select option')->extract(['value']);
        foreach ($monthLinks as $monthLink) {
            $getLinksForMonths($monthLink);
        }
    }, function(Exception $e){
        echo $e->getMessage();
    });



$loop->run();
