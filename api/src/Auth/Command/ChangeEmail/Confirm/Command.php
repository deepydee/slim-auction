<?php

declare(strict_types=1);

namespace App\Auth\Command\ChangeEmail\Confirm;

final readonly class Command
{
    public function __construct(public string $token)
    {
    }
}
