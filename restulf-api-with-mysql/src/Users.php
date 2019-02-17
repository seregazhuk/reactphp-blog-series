<?php

namespace App;

use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;
use React\Promise\PromiseInterface;

final class Users
{
    private $db;

    public function __construct(ConnectionInterface $db)
    {
        $this->db = $db;
    }

    public function all(): PromiseInterface
    {
        return $this->db->query('SELECT id, name, email FROM users ORDER BY id')
            ->then(function (QueryResult $queryResult) {
                return $queryResult->resultRows;
            });
    }

    public function find(string $id): PromiseInterface
    {
        return $this->db->query('SELECT id, name, email FROM users WHERE id = ?', [$id])
            ->then(function (QueryResult $result) {
                if (empty($result->resultRows)) {
                    throw new UserNotFoundError();
                }

                return $result->resultRows[0];
            });
    }

    public function update(string $id, string $newName): PromiseInterface
    {
        return $this->find($id)
            ->then(function () use ($id, $newName) {
                $this->db->query('UPDATE users SET name = ? WHERE id = ?', [$newName, $id]);
            });
    }

    public function delete(string $id): PromiseInterface
    {
        return $this->db
            ->query('DELETE FROM users WHERE id = ?', [$id])
            ->then(
                function (QueryResult $result) {
                    if ($result->affectedRows === 0) {
                        throw new UserNotFoundError();
                    }
                });
    }

    public function create(string $name, string $email): PromiseInterface
    {
        return $this->db->query('INSERT INTO users(name, email) VALUES (?, ?)', [$name, $email]);
    }
}
