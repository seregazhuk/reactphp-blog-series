<?php

namespace App\Controller;

use App\JsonResponse;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use React\MySQL\ConnectionInterface;

final class CreateUser
{
    private $db;

    public function __construct(ConnectionInterface $db)
    {
        $this->db = $db;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $user = json_decode((string) $request->getBody(), true);

        return $this->db->query('INSERT INTO users(name, email) VALUES (?, ?)', $user)
            ->then(
                function () {
                    return JsonResponse::created();
                },
                function (Exception $error) {
                    return JsonResponse::badRequest($error->getMessage());
                }
            );
    }
}
