<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\Id;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(Id::class)]
final class IdTest extends TestCase
{
    #[Test]
    public function it_can_success(): void
    {
        $id = new Id($value = Uuid::uuid4()->toString());

        self::assertEquals($value, $id->value());
    }

    #[Test]
    public function it_can_process_case(): void
    {
        $value = Uuid::uuid4()->toString();
        $id = new Id(mb_strtoupper($value));

        self::assertEquals($value, $id->value());
    }

    #[Test]
    public function it_can_return_next_id(): void
    {
        $id = Id::next();

        self::assertNotEmpty($id->value());
    }

    #[Test]
    public function it_can_validate_incorrect_value(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Id('12345');
    }

    #[Test]
    public function it_cannot_be_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Id('');
    }
}
