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
        $connection->write("Hi\n");
        $this->initEvents($connection);
        $this->connections->attach($connection);
    }

    /**
     * @param ConnectionInterface $connection
     */
    protected function initEvents(ConnectionInterface $connection)
    {
        // On receiving the data we loop through other connections
        // end write this data to them
        $connection->on('data', function ($data) use ($connection) {
            foreach ($this->connections as $conn) {
                if ($conn == $connection) continue;

                $conn->write($data);
            }
        });

        // When connection closes detach it from the loop
        $connection->on('close', function($connection){
            $this->connections->detach($connection);
        });
    }
}


$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server('127.0.0.1:8080', $loop);
$pool = new ConnectionsPool();

$socket->on('connection', function(ConnectionInterface $connection) use ($pool){
    $pool->add($connection);
});
$loop->run();