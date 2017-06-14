<?php

require '../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

$loop->addTimer(2, function() {
    echo "Hello world\n";
});

$loop->run();
echo "finished\n";