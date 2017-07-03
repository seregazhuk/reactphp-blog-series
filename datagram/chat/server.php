<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$factory = new React\Datagram\Factory($loop);

$address = 'localhost:1234';

class UdpChatServer {

    protected $clients = [];

    /**
     * @var \React\Datagram\Socket
     */
    protected $socket;

    public function __construct(\React\Datagram\Socket $socket)
    {
        $this->socket = $socket;
    }

    public function process($data, $address)
    {
        $data = json_decode($data, true);

        if($data['type'] == 'enter') {
            $this->add($data['name'], $address); return;
        }

        if($data['type'] == 'leave') {
            $this->remove($address); return;
        }

        $this->broadcast($data['message'], $address);
    }

    public function add($name, $address)
    {
        if(!array_key_exists($address, $this->clients)) {
            $this->clients[$address] = $name;
        }

        $this->broadcast("$name enters chat", $address);

        return $this;
    }

    public function remove($address)
    {
        if(!array_key_exists($address, $this->clients)) return;

        $name = $this->clients[$address];

        unset($this->clients[$address]);

        $this->broadcast("$name leaves chat");
    }

    public function broadcast($message, $except = null)
    {
        foreach ($this->clients as $address => $name) {
            if($address == $except) continue;

            $this->socket->send($message, $address);
        }
    }
}

$factory->createServer($address)
    ->then(
        function (React\Datagram\Socket $server) {
            $udpServer = new UdpChatServer($server);

            $server->on('message', [$udpServer, 'process']);
        },
        function(Exception $error) {
            echo "ERROR: {$error->getMessage()}\n";
        });

echo "Listening on $address\n";
$loop->run();
