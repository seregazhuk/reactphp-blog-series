<?php

namespace App;

use React\Http\Response;

final class JsonResponse extends Response
{
    public function __construct(int $statusCode, $data = null)
    {
        $body = $data ? json_encode($data) : null;

        parent::__construct($statusCode, ['Content-Type' => 'application/json'], $body);
    }

    public static function ok($data = null): self
    {
        return new self(200, $data);
    }

    public static function noContent(): self
    {
        return new self(204);
    }

    public static function created(): self
    {
        return new self(201);
    }

    public static function badRequest(string $error): self
    {
        return new self(400, ['error' => $error]);
    }

    public static function notFound(string $error): self
    {
        return new self(404, ['error' => $error]);
    }

    public static function unauthorized(): self
    {
        return new self(401);
    }
}
