<?php

require  '../vendor/autoload.php';

use React\Socket\ConnectionInterface;

class ConnectionsPool {

    /** @var SplObjectStorage  */
    protected $connections;

    public function __construct()
    {
        $this->connections = new SplObjectStorage();
    }

    public function add(ConnectionInterface $connection)
    {
        $connection->write("Enter your name: ");
        $this->initEvents($connection);
        $this->setConnectionData($connection, []);
    }

    /**
     * @param ConnectionInterface $connection
     */
    protected function initEvents(ConnectionInterface $connection)
    {
        // On receiving the data we loop through other connections
        // from the pool and write this data to them
        $connection->on('data', function ($data) use ($connection) {
            $connectionData = $this->getConnectionData($connection);

            // It is the first data received, so we consider it as
            // a users name.
            if(empty($connectionData)) {
                $this->addNewMember($data, $connection);
                return;
            }

            $name = $this->getColoredMessage("0;36", $connectionData['name']);
            $this->sendAll("$name: $data", $connection);
        });

        // When connection closes detach it from the pool
        $connection->on('close', function() use ($connection){
            $data = $this->getConnectionData($connection);
            $name = $data['name'] ?? '';

            $this->connections->offsetUnset($connection);
            $this->sendAll(
                $this->getColoredMessage("1;31", "User $name leaves the chat") . "\n",
                $connection
            );
        });
    }

    /**
     * @param string $name
     * @param ConnectionInterface $connection
     */
    protected function addNewMember($name, $connection)
    {
        $name = str_replace(["\n", "\r"], "", $name);
        $this->setConnectionData($connection, ['name' => $name]);
        $this->sendAll(
            $this->getColoredMessage("0;32", "User $name joins the chat") . "\n",
            $connection
        );
    }

    /**
     * @param string $hexColor
     * @param string $message
     * @return string
     */
    private function getColoredMessage($hexColor, $message)
    {
        return "\033[{$hexColor}m{$message}\033[0m";
    }

    protected function setConnectionData(ConnectionInterface $connection, $data)
    {
        $this->connections->offsetSet($connection, $data);
    }

    protected function getConnectionData(ConnectionInterface $connection)
    {
        return $this->connections->offsetGet($connection);
    }

    /**
     * Send data to all connections from the pool except
     * the specified one.
     *
     * @param mixed $data
     * @param ConnectionInterface $except
     */
    protected function sendAll($data, ConnectionInterface $except) {
        foreach ($this->connections as $conn) {
            if ($conn != $except) $conn->write($data);
        }
    }
}


$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server('127.0.0.1:8080', $loop);
$pool = new ConnectionsPool();

$socket->on('connection', function(ConnectionInterface $connection) use ($pool){
    $pool->add($connection);
});

echo "Listening on {$socket->getAddress()}\n";

$loop->run();
