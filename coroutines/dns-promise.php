<?php

require __DIR__ . '/../vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();
$resolver = (new React\Dns\Resolver\Factory)->create('8.8.8.8', $loop);

function resolveDomainName($name, React\Dns\Resolver\Resolver $resolver)
{
    return $resolver
        ->resolve($name)
        ->then(function ($ip) use ($name) {
                echo 'Resolved "' . $name . '" to ' . $ip . PHP_EOL;
            },
            function (Exception $e) use ($name) {
                echo 'Failed to resolve "' . $name . '" - ' . $e->getMessage() . PHP_EOL;
            }
        );
}

\React\Promise\all([
    resolveDomainName('recoil.io', $resolver),
    resolveDomainName('reactphp.org', $resolver),
    resolveDomainName('probably-wont-resolve', $resolver),
])->then(
    function () {
        echo 'Done' . PHP_EOL;
    }
);
$loop->run();
