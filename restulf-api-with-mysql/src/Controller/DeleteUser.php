<?php

namespace App\Controller;

use App\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;

final class DeleteUser
{
    private $db;

    public function __construct(ConnectionInterface $db)
    {
        $this->db = $db;
    }

    public function __invoke(ServerRequestInterface $request, string $id)
    {
        return $this->db
            ->query('DELETE FROM users WHERE id = ?', [$id])
            ->then(
                function (QueryResult $result) {
                    return $result->affectedRows
                        ? JsonResponse::noContent()
                        : JsonResponse::notFound();
                }
        );
    }
}
