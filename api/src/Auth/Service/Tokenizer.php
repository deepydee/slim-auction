<?php

declare(strict_types=1);

namespace App\Auth\Service;

use App\Auth\Entity\User\Token;
use DateInterval;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

final readonly class Tokenizer
{
    public function __construct(private DateInterval $interval)
    {
    }

    public function generate(DateTimeImmutable $date): Token
    {
        return new Token(
            Uuid::uuid4()->toString(),
            $date->add($this->interval)
        );
    }
}
