<?php

namespace App\Auth;

use App\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;

final class Guard
{
    private $routesPattern;

    private $authenticator;

    public function __construct(string $routesPattern, JwtAuthenticator $authenticator)
    {
        $this->routesPattern = $routesPattern;
        $this->authenticator = $authenticator;
    }

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        if (!$this->tryToAuthenticate($request)) {
            return JsonResponse::unauthorized();
        }

        return $next($request);

    }

    private function tryToAuthenticate(ServerRequestInterface $request): bool
    {
        $path = $request->getUri()->getPath();
        if (preg_match("~$this->routesPattern~", $path, $matches) === 0) {
            return true;
        }


        return $this->authenticator->validate($request);
    }
}
