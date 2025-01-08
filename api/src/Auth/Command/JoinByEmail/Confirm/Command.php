<?php

declare(strict_types=1);

namespace App\Auth\Command\JoinByEmail\Confirm;

final readonly class Command
{
    public function __construct(
        public string $token,
    ) {
    }
}
