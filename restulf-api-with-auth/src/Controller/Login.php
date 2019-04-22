<?php

namespace App\Controller;

use App\Auth\JwtAuthenticator;
use App\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;

final class Login
{
    private $authenticator;

    public function __construct(JwtAuthenticator $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $params = json_decode((string) $request->getBody(), true);
        $email = $params['email'] ?? '';

        if ($email === null) {
            return JsonResponse::badRequest("Field 'email' is required");
        }

        return $this->authenticator->authenticate($email)
            ->then(
                function (string $token) {
                    return JsonResponse::ok(['token' => $token]);
                },
                function () {
                    return JsonResponse::unauthorized();
                });
    }
}
