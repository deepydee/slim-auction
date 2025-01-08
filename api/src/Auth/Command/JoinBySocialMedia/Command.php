<?php

declare(strict_types=1);

namespace App\Auth\Command\JoinBySocialMedia;

final readonly class Command
{
    public function __construct(
        public string $email,
        public string $socialMedia,
        public string $identity,
    ) {
    }
}
