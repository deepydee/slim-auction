<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Service;

use App\Auth\Service\PasswordHasher;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(PasswordHasher::class)]
final class PasswordHasherTest extends TestCase
{
    #[Test]
    public function it_can_hash_password(): void
    {
        $hasher = new PasswordHasher(memoryCost: 8);

        $hash = $hasher->hash($password = 'new-password');

        self::assertNotEmpty($hash);
        self::assertNotEquals($password, $hash);
    }

    #[Test]
    public function password_cannot_be_empty(): void
    {
        $hasher = new PasswordHasher(memoryCost: 8);

        $this->expectException(InvalidArgumentException::class);
        $hasher->hash('');
    }

    #[Test]
    public function hash_can_be_validated(): void
    {
        $hasher = new PasswordHasher(memoryCost: 8);

        $hash = $hasher->hash($password = 'new-password');

        self::assertTrue($hasher->validate($password, $hash));
        self::assertFalse($hasher->validate('wrong-password', $hash));
    }
}
