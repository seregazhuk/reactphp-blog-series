<?php

require_once __DIR__ . '/../../vendor/autoload.php';

class UdpChatServer
{
    protected $clients = [];

    /**
     * @var \React\Datagram\Socket
     */
    protected $socket;

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
        if (!array_key_exists($address, $this->clients)) {
            $this->clients[$address] = $name;
        }

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

        $this->broadcast("$name: $message", $address);
    }

    public function run()
    {
        $loop = React\EventLoop\Factory::create();
        $factory = new React\Datagram\Factory($loop);
        $address = 'localhost:1234';

        $factory->createServer($address)->then(
                function (React\Datagram\Socket $server) {
                    $this->socket = $server;
                    $server->on('message', [$this, 'process']);
                }, function (Exception $error) {
                echo "ERROR: {$error->getMessage()}\n";
            }
            );

        echo "Listening on $address\n";
        $loop->run();
    }
}

(new UdpChatServer())->run();

