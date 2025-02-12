<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User\ChangeEmail;

use App\Auth\Entity\User\Email;
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
final class ConfirmTest extends TestCase
{
    /**
     * @throws DateMalformedStringException
     */
    #[Test]
    public function user_can_change_email(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = new Token(Uuid::uuid4()->toString(), $now->modify('+1 day'));

        $user->requestEmailChanging($token, $now, $new = new Email('new-email@app.test'));

        self::assertNotNull($user->newEmailToken());

        $user->confirmEmailChanging($token->value(), $now);

        self::assertNull($user->newEmail());
        /** @psalm-suppress DocblockTypeContradiction  */
        self::assertNull($user->newEmailToken());
        self::assertEquals($new, $user->email());
    }

    /**
     * @throws DateMalformedStringException
     */
    #[Test]
    public function user_cannot_change_email_if_token_is_invalid(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = new Token(Uuid::uuid4()->toString(), $now->modify('+1 day'));

        $user->requestEmailChanging($token, $now, new Email('new-email@app.test'));

        $this->expectExceptionMessage('Token is invalid.');
        $user->confirmEmailChanging('invalid', $now);
    }

    /**
     * @throws DateMalformedStringException
     */
    #[Test]
    public function user_cannot_change_email_if_token_is_expired(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = new Token(Uuid::uuid4()->toString(), $now);

        $user->requestEmailChanging($token, $now, new Email('new-email@app.test'));

        $this->expectExceptionMessage('Token is expired.');
        $user->confirmEmailChanging($token->value(), $now->modify('+1 day'));
    }

    /**
     * @throws DateMalformedStringException
     */
    #[Test]
    public function user_cannot_change_email_if_changing_is_not_requested(): void
    {
        $user = (new UserBuilder())->active()->build();

        $now = new DateTimeImmutable();
        $token = new Token(Uuid::uuid4()->toString(), $now->modify('+1 day'));

        $this->expectExceptionMessage('Changing is not requested.');
        $user->confirmEmailChanging($token->value(), $now);
    }
}
