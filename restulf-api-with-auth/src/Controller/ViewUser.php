<?php

namespace App\Controller;

use App\JsonResponse;
use App\UserNotFoundError;
use App\Users;
use Psr\Http\Message\ServerRequestInterface;

final class ViewUser
{
    private $users;

    public function __construct(Users $users)
    {
        $this->users = $users;
    }

    public function __invoke(ServerRequestInterface $request, string $id)
    {
        return $this->users->findById($id)
            ->then(
                function (array $user) {
                    return JsonResponse::ok($user);
                },
                function (UserNotFoundError $error) {
                    return JsonResponse::notFound($error->getMessage());
                }
            );
    }
}
