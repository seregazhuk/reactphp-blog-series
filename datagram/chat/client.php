<?php

use React\Datagram\Socket;
use React\EventLoop\LoopInterface;
use React\Stream\ReadableResourceStream;
use React\Stream\ReadableStreamInterface;

require_once __DIR__ . '/../../vendor/autoload.php';

class UdpChatClient
{
    /** @var  LoopInterface */
    protected $loop;

    /** @var ReadableStreamInterface  */
    protected $stdin;

    /** @var  Socket */
    protected $client;

    protected $name = '';

    public function run()
    {
        $this->loop = React\EventLoop\Factory::create();
        $factory = new React\Datagram\Factory($this->loop);

        $this->stdin = new ReadableResourceStream(STDIN, $this->loop);
        $this->stdin->on('data', [$this, 'processInput']);

        $factory->createClient('localhost:1234')
            ->then(
                [$this, 'initClient'],
                function (Exception $error) {
                    echo "ERROR: {$error->getMessage()}\n";
                });

        $this->loop->run();
    }

    public function initClient(Socket $client)
    {
        $this->client = $client;
        $this->initClientEvents();
        echo "Enter your name: ";
    }

    protected function initClientEvents()
    {
        $this->client->on('message', function ($message) {
            echo $message . "\n";
        });

        $this->client->on('close', function () {
            $this->loop->stop();
        });
    }

    public function processInput($data)
    {
        $data = trim($data);

        if (empty($this->name)) {
            $this->name = $data;
            $this->sendData('', 'enter');
            return;
        }

        if ($data == ':exit') {
            $this->sendData('', 'leave');
            $this->client->end();
        }

        $this->sendData($data);
    }

    protected function sendData($message, $type = 'message')
    {
        $data = [
            'message' => $message,
            'name'    => $this->name,
            'type'    => $type,
        ];

        $this->client->send(json_encode($data));
    }
}

(new UdpChatClient())->run();
