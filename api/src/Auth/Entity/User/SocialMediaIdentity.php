<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Webmozart\Assert\Assert;

final readonly class SocialMediaIdentity
{
    private string $name;
    private string $identity;

    public function __construct(string $name, string $identity)
    {
        Assert::notEmpty($name);
        Assert::notEmpty($identity);

        $this->name = mb_strtolower($name);
        $this->identity = mb_strtolower($identity);
    }

    public function isEqualTo(self $socialMedia): bool
    {
        return
            $this->name() === $socialMedia->name() &&
            $this->identity() === $socialMedia->identity();
    }

    public function name(): string
    {
        return $this->name;
    }

    public function identity(): string
    {
        return $this->identity;
    }
}
