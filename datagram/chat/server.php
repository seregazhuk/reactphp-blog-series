<?php

use React\Datagram\Socket;
use React\EventLoop\LoopInterface;

require_once __DIR__ . '/../../vendor/autoload.php';

class UdpChatServer
{
    /**
     * @var string
     */
    protected $address;

    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var array
     */
    protected $clients = [];

    /**
     * @var Socket
     */
    protected $socket;

    /**
     * @param string $address
     * @param LoopInterface $loop
     */
    public function __construct($address, LoopInterface $loop)
    {
        $this->address = $address;
        $this->loop = $loop;
    }

    public function process($data, $address)
    {
        $data = json_decode($data, true);

        if ($data['type'] == 'enter') {
            $this->addClient($data['name'], $address);
            return;
        }

        if ($data['type'] == 'leave') {
            $this->removeClient($address);
            return;
        }

        $this->sendMessage($data['message'], $address);
    }

    protected function addClient($name, $address)
    {
        if (array_key_exists($address, $this->clients)) return;

        $this->clients[$address] = $name;

        $this->broadcast("$name enters chat", $address);
    }

    protected function removeClient($address)
    {
        $name = $this->clients[$address] ?? '';

        unset($this->clients[$address]);

        $this->broadcast("$name leaves chat");
    }

    protected function broadcast($message, $except = null)
    {
        foreach ($this->clients as $address => $name) {
            if ($address == $except) continue;

            $this->socket->send($message, $address);
        }
    }

    protected function sendMessage($message, $address)
    {
        $name = $this->clients[$address] ?? '';

        $this->broadcast(Output::message($name, $message), $address);
    }

    public function run()
    {
        $factory = new React\Datagram\Factory($this->loop);
        $factory->createServer($this->address)
            ->then(
                function (React\Datagram\Socket $server) {
                    $this->socket = $server;
                    $server->on('message', [$this, 'process']);
                },
                function (Exception $error) {
                    echo "ERROR: {$error->getMessage()}\n";
                }
            );

        echo "Listening on $this->address\n";
        $this->loop->run();
    }
}

$loop = React\EventLoop\Factory::create();
(new UdpChatServer('localhost:1234', $loop))->run();

