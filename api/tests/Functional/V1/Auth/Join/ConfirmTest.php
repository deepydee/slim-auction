<?php

declare(strict_types=1);

namespace Test\Functional\V1\Auth\Join;

use JsonException;
use PHPUnit\Framework\Attributes\Test;
use Ramsey\Uuid\Uuid;
use Test\Functional\Json;
use Test\Functional\WebTestCase;

/**
 * @internal
 */
final class ConfirmTest extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures([
            ConfirmFixture::class,
        ]);
    }

    /**
     * @throws JsonException
     */
    #[Test]
    public function it_cannot_be_accessed_by_get(): void
    {
        $response = $this->app()->handle(self::json('GET', '/v1/auth/join/confirm'));

        self::assertEquals(405, $response->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    #[Test]
    public function test_success(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/auth/join/confirm', [
            'token' => ConfirmFixture::VALID,
        ]));

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('', (string) $response->getBody());
    }

    /**
     * @throws JsonException
     */
    #[Test]
    public function test_expired(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/auth/join/confirm', [
            'token' => ConfirmFixture::EXPIRED,
        ]));

        self::assertEquals(409, $response->getStatusCode());
        self::assertJson($body = (string) $response->getBody());

        self::assertEquals([
            'message' => 'Token is expired.',
        ], Json::decode($body));
    }

    /**
     * @throws JsonException
     */
    #[Test]
    public function test_empty(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/auth/join/confirm', []));

        self::assertEquals(409, $response->getStatusCode());
        self::assertJson($body = (string) $response->getBody());

        self::assertEquals([
            'message' => 'Invalid token.',
        ], Json::decode($body));
    }

    /**
     * @throws JsonException
     */
    #[Test]
    public function test_not_existing(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/auth/join/confirm', [
            'token' => Uuid::uuid4()->toString(),
        ]));

        self::assertEquals(409, $response->getStatusCode());
        self::assertJson($body = (string) $response->getBody());

        self::assertEquals([
            'message' => 'Invalid token.',
        ], Json::decode($body));
    }
}
