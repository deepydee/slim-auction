<?php

declare(strict_types=1);

namespace App\Auth\Command\ChangPassword;

final readonly class Command
{
    public function __construct(
        public string $id,
        public string $currentPassword,
        public string $newPassword,
    ) {
    }
}
