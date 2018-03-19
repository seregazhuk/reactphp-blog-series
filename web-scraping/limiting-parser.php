<?php

require '../vendor/autoload.php';

use Clue\React\Buzz\Browser;
use React\Promise\Promise;

class QueueableParser extends Parser
{
    public function parse(array $urls = [], $timeout = 5, $concurrencyLimit = 10)
    {
        $queue = $this->initQueue($concurrencyLimit);

        foreach ($urls as $url) {
            /** @var Promise $promise */
            $promise = $queue($url)->then(
                function (\Psr\Http\Message\ResponseInterface $response) {
                    $this->parsed[] = $this->extractFromHtml((string)$response->getBody());
                }
            );

            $this->loop->addTimer($timeout, function () use ($promise) {
                $promise->cancel();
            });
        }
    }

    /**
     * @param int $concurrencyLimit
     * @return \Clue\React\Mq\Queue
     */
    protected function initQueue($concurrencyLimit)
    {
        return new Clue\React\Mq\Queue($concurrencyLimit, null, function ($url) {
            return $this->client->get($url);
        });
    }
}


$loop = React\EventLoop\Factory::create();
$client = new Browser($loop);

$parser = new Parser($client, $loop);
$parser->parse([
    'http://www.imdb.com/title/tt1270797/',
    'http://www.imdb.com/title/tt2527336/',
], 20, 1);

$loop->run();
print_r($parser->getMovieData());
