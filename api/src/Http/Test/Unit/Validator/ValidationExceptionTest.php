<?php

declare(strict_types=1);

namespace App\Http\Test\Unit\Validator;

use App\Http\Validator\ValidationException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * @internal
 */
#[CoversClass(ValidationException::class)]
final class ValidationExceptionTest extends TestCase
{
    #[Test]
    public function validation_exception_can_carry_violations(): void
    {
        $exception = new ValidationException(
            $violations = new ConstraintViolationList()
        );

        self::assertEquals('Invalid input.', $exception->getMessage());
        self::assertEquals($violations, $exception->getViolations());
    }
}
