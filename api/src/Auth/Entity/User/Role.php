<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use InvalidArgumentException;

enum Role: string
{
    case User = 'user';
    case Admin = 'admin';

    public static function make(string $role): self
    {
        return match ($role) {
            'user' => self::User,
            'admin' => self::Admin,
            default => throw new InvalidArgumentException('Invalid role.'),
        };
    }
}
