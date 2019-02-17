<?php

namespace App\Controller;

use App\JsonResponse;
use App\Users;
use Psr\Http\Message\ServerRequestInterface;

final class ListUsers
{
    private $users;

    public function __construct(Users $users)
    {
        $this->users = $users;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        return $this->users->all()
            ->then(function(array $users) {
                return JsonResponse::ok($users);
            });
    }
}
