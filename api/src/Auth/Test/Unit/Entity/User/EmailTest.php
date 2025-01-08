<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\Email;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Email::class)]
final class EmailTest extends TestCase
{
    #[Test]
    public function it_can_be_constructed(): void
    {
        $email = new Email($value = 'email@app.test');

        self::assertEquals($value, $email->value());
    }

    #[Test]
    public function it_can_process_case(): void
    {
        $email = new Email('EmAil@app.test');

        self::assertEquals('email@app.test', $email->value());
    }

    #[Test]
    public function it_can_validate_incorrect_value(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Email('not-email');
    }

    #[Test]
    public function it_cannot_be_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Email('');
    }
}
