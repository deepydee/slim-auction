<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User;

use App\Auth\Entity\User\User;
use App\Auth\Service\PasswordHasher;
use App\Auth\Test\Builder\UserBuilder;
use DateMalformedStringException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(User::class)]
final class ChangePasswordTest extends TestCase
{
    /**
     * @throws DateMalformedStringException
     */
    #[Test]
    public function user_can_change_password(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $hasher = $this->createHasher(true, $hash = 'new-hash');

        $user->changePassword(
            'old-password',
            'new-password',
            $hasher,
        );

        self::assertEquals($hash, $user->passwordHash());
    }

    /**
     * @throws DateMalformedStringException
     * @throws Exception
     */
    #[Test]
    public function user_cannot_change_password_if_current_password_is_incorrect(): void
    {
        $user = (new UserBuilder())
            ->active()
            ->build();

        $hasher = $this->createHasher(false, 'new-hash');

        $this->expectExceptionMessage('Incorrect current password.');
        $user->changePassword(
            'wrong-old-password',
            'new-password',
            $hasher,
        );
    }

    /**
     * @throws Exception
     * @throws DateMalformedStringException
     */
    #[Test]
    public function user_cannot_change_password_without_old_password(): void
    {
        $user = (new UserBuilder())
            ->viaSocialMedia()
            ->build();

        $hasher = $this->createHasher(false, 'new-hash');

        $this->expectExceptionMessage('User does not have an old password.');
        $user->changePassword(
            'any-old-password',
            'new-password',
            $hasher,
        );
    }

    /**
     * @throws Exception
     */
    private function createHasher(bool $valid, string $hash): PasswordHasher
    {
        $hasher = self::createStub(PasswordHasher::class);
        $hasher->method('validate')->willReturn($valid);
        $hasher->method('hash')->willReturn($hash);

        return $hasher;
    }
}
