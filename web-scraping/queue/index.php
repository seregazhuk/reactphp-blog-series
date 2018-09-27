<?php

use Clue\React\Buzz\Browser;

require __DIR__ . '/../../vendor/autoload.php';
require 'queueable-parser.php';

$loop = React\EventLoop\Factory::create();
$client = new Browser($loop);

$parser = new QueueableParser($client, $loop);
$parser->parse([
    'http://www.imdb.com/title/tt1270797/',
    'http://www.imdb.com/title/tt2527336/',
], 20, 1);

$loop->run();
print_r($parser->getMovieData());
