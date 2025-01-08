<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\Token;

use App\Auth\Entity\User\Token;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @internal
 */
#[CoversClass(Token::class)]
final class CreateTest extends TestCase
{
    #[Test]
    public function it_can_generate_token(): void
    {
        $token = new Token(
            $value = Uuid::uuid4()->toString(),
            $expires = new DateTimeImmutable()
        );

        self::assertEquals($value, $token->value());
        self::assertEquals($expires, $token->expiresAt());
    }

    #[Test]
    public function it_can_process_case(): void
    {
        $value = Uuid::uuid4()->toString();
        $token = new Token(mb_strtoupper($value), new DateTimeImmutable());

        self::assertEquals($value, $token->value());
    }

    #[Test]
    public function it_can_validate_incorrect_value(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Token('12345', new DateTimeImmutable());
    }

    #[Test]
    public function it_cannot_be_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Token('', new DateTimeImmutable());
    }
}
