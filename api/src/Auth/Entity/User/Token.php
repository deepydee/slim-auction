<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use RuntimeException;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
final readonly class Token
{
    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $value;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $expiresAt;

    public function __construct(string $value, DateTimeImmutable $expires)
    {
        Assert::uuid($value);
        $this->value = mb_strtolower($value);
        $this->expiresAt = $expires;
    }

    public function validate(string $value, DateTimeImmutable $date): void
    {
        if (! $this->isEqualTo($value)) {
            throw new DomainException('Token is invalid.');
        }

        if ($this->isExpiredTo($date)) {
            throw new DomainException('Token is expired.');
        }
    }

    private function isEqualTo(string $value): bool
    {
        return $this->value === $value;
    }

    public function value(): string
    {
        return $this->value ?? throw new RuntimeException('Empty value.');
    }

    public function expiresAt(): DateTimeImmutable
    {
        return $this->expiresAt  ?? throw new RuntimeException('Empty value.');
    }

    public function isExpiredTo(DateTimeImmutable $date): bool
    {
        return $this->expiresAt <= $date;
    }

    public function isEmpty(): bool
    {
        return empty($this->value);
    }
}
