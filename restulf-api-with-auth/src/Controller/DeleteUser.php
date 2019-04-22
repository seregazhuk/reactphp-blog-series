<?php

namespace App\Controller;

use App\JsonResponse;
use App\UserNotFoundError;
use App\Users;
use Psr\Http\Message\ServerRequestInterface;

final class DeleteUser
{
    private $users;

    public function __construct(Users $users)
    {
        $this->users = $users;
    }

    public function __invoke(ServerRequestInterface $request, string $id)
    {
        return $this->users->delete($id)
            ->then(
                function () {
                    return JsonResponse::noContent();
                },
                function (UserNotFoundError $error) {
                    return JsonResponse::notFound($error->getMessage());
                }
            );
    }
}
