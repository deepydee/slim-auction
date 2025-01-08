<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User\JoinByEmail;

use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use App\Auth\Test\Builder\UserBuilder;
use Ramsey\Uuid\Uuid;

#[CoversClass(User::class)]
final class ConfirmTest extends TestCase
{
    /**
     * @throws \DateMalformedStringException
     */
    #[Test]
    public function user_can_successfully_confirm_their_email(): void
    {
        $user = (new UserBuilder())
            ->withJoinConfirmationToken($token = $this->createToken())
            ->build();

        self::assertTrue($user->isWait());
        self::assertFalse($user->isActive());

        $user->confirmJoin($token->value(), $token->expiresAt()->modify('-1 day'));

        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());

        self::assertNull($user->joinConfirmationToken());
    }

    /**
     * @throws \DateMalformedStringException
     */
    #[Test]
    public function user_cannot_confirm_email_with_invalid_token(): void
    {
        $user = (new UserBuilder())
            ->withJoinConfirmationToken($token = $this->createToken())
            ->build();

        $this->expectExceptionMessage('Token is invalid.');

        $user->confirmJoin(Uuid::uuid4()->toString(), $token->expiresAt()->modify('-1 day'));
    }

    /**
     * @throws \DateMalformedStringException
     */
    #[Test]
    public function user_cannot_confirm_email_with_expired_token(): void
    {
        $user = (new UserBuilder())
            ->withJoinConfirmationToken($token = $this->createToken())
            ->build();

        $this->expectExceptionMessage('Token is expired.');

        $user->confirmJoin($token->value(), $token->expiresAt()->modify('+1 day'));
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function user_cannot_confirm_email_if_it_already_confirmed(): void
    {
        $token = $this->createToken();

        $user = (new UserBuilder())
            ->withJoinConfirmationToken($token)
            ->active()
            ->build();

        $this->expectExceptionMessage('Confirmation is not required.');

        $user->confirmJoin($token->value(), $token->expiresAt()->modify('-1 day'));
    }

    private function createToken(): Token
    {
        return new Token(
            Uuid::uuid4()->toString(),
            new \DateTimeImmutable('+1 day')
        );
    }
}
