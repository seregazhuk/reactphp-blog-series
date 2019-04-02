<?php

use Clue\React\Buzz\Browser;
use Psr\Http\Message\ResponseInterface;
use React\Filesystem\FilesystemInterface;
use Symfony\Component\DomCrawler\Crawler;

final class ScraperForImages
{
    private $client;

    private $filesystem;

    private $directory;

    public function __construct(Browser $client, FilesystemInterface $filesystem, string $directory)
    {
        $this->client = $client;
        $this->filesystem = $filesystem;
        $this->directory = $directory;
    }

    public function scrape(string ...$urls)
    {
        foreach ($urls as $url) {
            $this->client->get($url)->then(
                function (ResponseInterface $response) {
                    $this->processResponse((string) $response->getBody());
                });
        }
    }

    private function processResponse(string $html)
    {
        $crawler = new Crawler($html);
        $imageUrl = $crawler->filter('.image-section__image')->attr('src');
        preg_match('/photos\/\d+\/([\w-\.]+)\?/', $imageUrl, $matches);

        $filePath = $this->directory . DIRECTORY_SEPARATOR . $matches[1];

        $this->client->get($imageUrl)->then(
            function(ResponseInterface $response) use ($filePath) {
                $this->filesystem->file($filePath)->putContents((string)$response->getBody());
        });
    }
}
