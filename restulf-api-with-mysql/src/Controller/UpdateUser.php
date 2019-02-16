<?php

namespace App\Controller;

use App\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;

final class UpdateUser
{
    private $db;

    public function __construct(ConnectionInterface $db)
    {
        $this->db = $db;
    }

    public function __invoke(ServerRequestInterface $request, string $id)
    {
        $name = $this->extractName($request);
        if (empty($name)) {
            return JsonResponse::badRequest('"name" field is required');
        }

        return $this->db
            ->query('UPDATE users SET name = ? WHERE id = ?', [$name, $id])
            ->then(function (QueryResult $result) {
                return $result->affectedRows
                    ? JsonResponse::noContent()
                    : JsonResponse::notFound();
            }
        );
    }

    private function extractName(ServerRequestInterface $request): ?string
    {
        $params = json_decode((string)$request->getBody(), true);
        return $params['name'] ?? null;
    }
}
