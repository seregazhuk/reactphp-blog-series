<?php

use Recoil\Recoil;

require __DIR__ . '/../vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();

function resolveDomainName($name, React\Dns\Resolver\Resolver $resolver)
{
    try {
        $ip = yield $resolver->resolve($name);
        echo 'Resolved "' . $name . '" to ' . $ip . PHP_EOL;
    } catch (Exception $e) {
        echo 'Failed to resolve "' . $name . '" - ' . $e->getMessage() . PHP_EOL;
    }
}

\Recoil\React\ReactKernel::start(
    function () {
        $resolver = (new React\Dns\Resolver\Factory)->create(
            '8.8.8.8', yield Recoil::eventLoop()
        );

        yield [
            resolveDomainName('recoil.io', $resolver),
            resolveDomainName('reactphp.org', $resolver),
            resolveDomainName('probably-wont-resolve', $resolver),
        ];
        echo 'Done' . PHP_EOL;
    }
);

