<?php

require '../vendor/autoload.php';

use Clue\React\Buzz\Browser;
use Clue\React\Mq\Queue;

$loop = React\EventLoop\Factory::create();
$browser = new Browser($loop);

$queue = new Queue(2, null, function($url) use ($browser) {
    return $browser->get($url);
});

$urls = [
    'http://www.imdb.com/title/tt1270797/',
    'http://www.imdb.com/title/tt2527336/',
];

foreach ($urls as $url) {
    $queue($url)->then(function($response) {
        echo $response;
    });
}

$loop->run();