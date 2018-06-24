<?php

require '../../vendor/autoload.php';
require 'parser.php';

use Clue\React\Buzz\Browser;
use React\Socket\Connector;
use Clue\React\Socks\Client as SocksClient;

$loop = React\EventLoop\Factory::create();

$proxy = new SocksClient('184.178.172.13:15311', new Connector($loop));
$connector = new Connector($loop, ['tcp' => $proxy]);

$client = new Browser($loop, $connector);

$parser = new Parser($client, $loop);
$parser->parse([
    'http://www.imdb.com/title/tt1270797/',
    'http://www.imdb.com/title/tt2527336/',
    // ...
], 40);

$loop->run();
print_r($parser->getMovieData());
