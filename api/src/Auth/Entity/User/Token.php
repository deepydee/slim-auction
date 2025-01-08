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

    public function validate(string $value, DateTimeImmutable $date): void
    {
        if (! $this->isEqualTo($value)) {
            throw new \DomainException('Token is invalid.');
        }

        if ($this->isExpiredTo($date)) {
            throw new \DomainException('Token is expired.');
        }
    }

    private function isEqualTo(string $value): bool
    {
        return $this->value === $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function expiresAt(): DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function isExpiredTo(\DateTimeImmutable $date): bool
    {
        return $this->expiresAt <= $date;
    }
}
