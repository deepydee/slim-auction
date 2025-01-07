<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

final readonly class Token
{
    private string $value;
    private DateTimeImmutable $expiresAt;

    public function __construct(string $value, DateTimeImmutable $expires)
    {
        Assert::uuid($value);
        $this->value = mb_strtolower($value);
        $this->expiresAt = $expires;
    }

    public static function make(DateTimeImmutable $expiresAt): self
    {
        return new self(Uuid::uuid4()->toString(), $expiresAt);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function expiresAt(): DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt < new DateTimeImmutable();
    }
}
