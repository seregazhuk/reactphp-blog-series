<?php

namespace App;

use RuntimeException;

final class UserNotFoundError extends RuntimeException
{
    public function __construct($message = 'User not found')
    {
       parent::__construct($message);
    }
}
