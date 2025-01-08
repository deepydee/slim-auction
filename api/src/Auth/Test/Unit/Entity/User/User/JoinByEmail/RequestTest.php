<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User\JoinByEmail;

use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\User;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(User::class)]
final class RequestTest extends TestCase
{
    #[Test]
    public function it_can_create_user(): void
    {
        $user = new User(
            $id = Id::next(),
            $date = new DateTimeImmutable(),
            $email = new Email('mail@example.com'),
            $hash = 'hash',
            $token = new Token(Uuid::uuid4()->toString(), new DateTimeImmutable())
        );

        self::assertEquals($id, $user->id());
        self::assertEquals($date, $user->date());
        self::assertEquals($email, $user->email());
        self::assertEquals($hash, $user->passwordHash());
        self::assertEquals($token, $user->joinConfirmToken());

        self::assertTrue($user->isWait());
        self::assertFalse($user->isActive());
    }
}
