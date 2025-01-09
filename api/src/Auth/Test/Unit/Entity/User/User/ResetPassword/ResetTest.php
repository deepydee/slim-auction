<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User\ResetPassword;

use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use App\Auth\Test\Builder\UserBuilder;
use DateMalformedStringException;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @internal
 */
#[CoversClass(User::class)]
final class ResetTest extends TestCase
{
    /**
     * @throws DateMalformedStringException
     */
    #[Test]
    public function user_can_reset_password(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));

        $user->requestPasswordReset($token, $now);

        self::assertNotNull($user->passwordResetToken());

        $user->resetPassword($token->value(), $now, $hash = 'hash');

        self::assertNull($user->passwordResetToken());
        self::assertEquals($hash, $user->passwordHash());
    }

    /**
     * @throws DateMalformedStringException
     */
    #[Test]
    public function user_cannot_reset_password_with_invalid_token(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));

        $user->requestPasswordReset($token, $now);

        $this->expectExceptionMessage('Token is invalid.');
        $user->resetPassword(Uuid::uuid4()->toString(), $now, 'hash');
    }

    /**
     * @throws DateMalformedStringException
     */
    #[Test]
    public function user_cannot_reset_password_with_expired_token(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));

        $user->requestPasswordReset($token, $now);

        $this->expectExceptionMessage('Token is expired.');
        $user->resetPassword($token->value(), $now->modify('+1 day'), 'hash');
    }

    /**
     * @throws DateMalformedStringException
     */
    #[Test]
    public function user_cannot_reset_password_if_it_is_not_requested(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();

        $this->expectExceptionMessage('Resetting is not requested.');
        $user->resetPassword(Uuid::uuid4()->toString(), $now, 'hash');
    }

    private function createToken(DateTimeImmutable $date): Token
    {
        return new Token(
            Uuid::uuid4()->toString(),
            $date
        );
    }
}
