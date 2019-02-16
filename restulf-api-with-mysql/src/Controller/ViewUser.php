<?php

namespace App\Controller;

use App\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;

final class ViewUser
{
    private $db;

    public function __construct(ConnectionInterface $db)
    {
        $this->db = $db;
    }

    public function __invoke(ServerRequestInterface $request, string $id)
    {
        return $this->db
            ->query('SELECT id, name, email FROM users WHERE id = ?', [$id])
            ->then(function (QueryResult $result) {
                return empty($result->resultRows)
                    ? JsonResponse::notFound()
                    : JsonResponse::ok($result->resultRows);
            });
    }
}
