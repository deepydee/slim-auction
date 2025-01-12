<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
final class SocialMedia
{
    #[ORM\Column(type: Types::STRING, length: 16)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 16)]
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
