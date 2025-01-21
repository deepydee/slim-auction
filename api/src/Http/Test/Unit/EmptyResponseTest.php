<?php

declare(strict_types=1);

namespace App\Http\Test\Unit;

use App\Http\EmptyResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(EmptyResponse::class)]
final class EmptyResponseTest extends TestCase
{
    #[Test]
    public function empty_response_can_be_created(): void
    {
        $response = new EmptyResponse();

        self::assertEquals(204, $response->getStatusCode());
        self::assertFalse($response->hasHeader('Content-Type'));

        self::assertEquals('', (string) $response->getBody());
        self::assertFalse($response->getBody()->isWritable());
    }

    public function empty_response_can_carry_custom_code(): void
    {
        $response = new EmptyResponse(201);

        self::assertEquals(201, $response->getStatusCode());
    }
}
