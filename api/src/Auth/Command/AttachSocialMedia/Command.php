<?php

declare(strict_types=1);

namespace App\Auth\Command\AttachSocialMedia;

final readonly class Command
{
    public function __construct(
        public string $id,
        public string $socialMedia,
        public string $identity,
    ) {
    }
}
