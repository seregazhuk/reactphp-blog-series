<?php

namespace App\Controller;

use App\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use React\MySQL\ConnectionInterface;

final class ListUsers
{
    private $db;

    public function __construct(ConnectionInterface $db)
    {
        $this->db = $db;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        return $this->db->query('SELECT id, name, email FROM users ORDER BY id')
            ->then(function (\React\MySQL\QueryResult $queryResult) {
                return JsonResponse::ok($queryResult->resultRows);
            }
        );
    }
}
