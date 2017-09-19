<?php

require  '../vendor/autoload.php';

use React\Socket\ConnectionInterface;

class Output {
    public static function warning($message)
    {
        return self::getColoredMessage("01;31", $message);
    }

    public static function info($message)
    {
        return self::getColoredMessage("0;32", $message);
    }

    public static function message($text)
    {
        return self::getColoredMessage("0;36", $text);
    }

    /**
     * @param string $hexColor
     * @param string $message
     * @return string
     */
    private static function getColoredMessage($hexColor, $message)
    {
        return "\033[{$hexColor}m{$message}\033[0m";
    }
}

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

            // If it is the first data received, we add a new member,
	        // otherwise send a message
            empty($connectionData) ?
                $this->addNewMember($data, $connection) :
	            $this->sendMessage($connection, $connectionData['name'], $data);
        });

        // When connection closes detach it from the pool
        $connection->on('close', function() use ($connection){
            $data = $this->getConnectionData($connection);
            $name = $data['name'] ?? '';

            $this->connections->offsetUnset($connection);
            $this->sendAll(Output::warning("User $name leaves the chat") . "\n", $connection);
        });
    }

	protected function sendMessage(ConnectionInterface $connection, $name, $message)
	{
		// if is a private message
		preg_match('/^@(\w+):\s(.+)/', $message, $matches);
		if(!$matches) {
			$this->sendAll("$name: $message", $connection);
			return;
		}

		$sendTo = $matches[1];
		$wasSent = $this->sendTo($sendTo, $name . ': ' . $matches[2]);
		if(!$wasSent) {
			$connection->write(Output::warning("User $sendTo not found!"));
		}
    }

	protected function sendTo($name, $message)
	{
		$connection = $this->getConnectionByName($name);
		if(!$connection) return false;

		$connection->write(Output::message($message) . "\n");
		return true;
    }

    protected function checkIsUniqueName($name)
    {
        foreach ($this->connections as $obj) {
            $data = $this->connections->offsetGet($obj);
            $takenName = $data['name'] ?? '';
            if($takenName == $name) return false;
        }

        return true;
    }

    protected function addNewMember($name, ConnectionInterface $connection)
    {
        $name = str_replace(["\n", "\r"], "", $name);

        if($this->getConnectionByName($name)) {
            $connection->write(Output::warning("Name $name is already taken!") . "\n");
            $connection->write("Enter your name: ");
            return;
        }

        $this->setConnectionData($connection, ['name' => $name]);
        $this->sendAll(Output::info("User $name joins the chat") . "\n", $connection);
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

    /**
     * @param string $name
     * @return null|ConnectionInterface
     */
    protected function getConnectionByName($name)
    {
        /** @var ConnectionInterface $connection */
        foreach ($this->connections as $connection) {
            $data = $this->connections->offsetGet($connection);
            $takenName = $data['name'] ?? '';
            if($takenName == $name) return $connection;
        }

        return null;
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

