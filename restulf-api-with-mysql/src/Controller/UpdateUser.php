<?php

namespace App\Controller;

use App\JsonResponse;
use App\UserNotFoundError;
use App\Users;
use Psr\Http\Message\ServerRequestInterface;

final class UpdateUser
{
    private $users;

    public function __construct(Users $users)
    {
        $this->users = $users;
    }

    public function __invoke(ServerRequestInterface $request, string $id)
    {
        $name = $this->extractName($request);
        if (empty($name)) {
            return JsonResponse::badRequest('"name" field is required');
        }

        return $this->users->update($id, $name)
            ->then(
                function () {
                    return JsonResponse::noContent();
                },
                function (UserNotFoundError $error) {
                    return JsonResponse::notFound($error->getMessage());
                }
            );
    }

    private function extractName(ServerRequestInterface $request): ?string
    {
        $params = json_decode((string)$request->getBody(), true);
        return $params['name'] ?? null;
    }
}
