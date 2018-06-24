<?php

require '../vendor/autoload.php';

use Clue\React\Buzz\Browser;
use Psr\Http\Message\ResponseInterface;
use Clue\React\Socks\Client;
use React\Socket\Connector;

$loop = React\EventLoop\Factory::create();

$proxy = new Client('184.178.172.13:15311', new Connector($loop));
$client = new Browser($loop, new Connector($loop, ['tcp' => $proxy]));

$client->get('http://google.com/')
    ->then(function (ResponseInterface $response) {
        var_dump((string)$response->getBody());
    }, function (Exception $exception) {
        echo $exception->getMessage() . PHP_EOL;
    });

$loop->run();
