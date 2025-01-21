<?php

declare(strict_types=1);

namespace Test\Functional\V1\Auth\Join;

use App\Http\Action\V1\Auth\Join\RequestAction;
use JsonException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Test\Functional\Json;
use Test\Functional\WebTestCase;

/**
 * @internal
 */
#[CoversClass(RequestAction::class)]
final class RequestTest extends WebTestCase
{
    /**
     * @throws JsonException
     */
    #[Test]
    public function not_supported_response_if_method_is_not_post(): void
    {
        $response = $this->app()->handle(self::json('GET', '/v1/auth/join'));

        self::assertEquals(405, $response->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    #[Test]
    public function user_can_join_by_email(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/auth/join', [
            'email' => 'new-user@app.test',
            'password' => 'new-password',
        ]));

        self::assertEquals(201, $response->getStatusCode());
        self::assertEquals('', (string) $response->getBody());
    }

    /**
     * @throws JsonException
     */
    #[Test]
    public function user_with_an_existing_email_cannot_join(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/auth/join', [
            'email' => 'user@app.test',
            'password' => 'new-password',
        ]));

        self::assertEquals(409, $response->getStatusCode());
        self::assertJson($body = (string) $response->getBody());

        self::assertEquals([
            'message' => 'User already exists.',
        ], Json::decode($body));
    }

    /**
     * @throws JsonException
     */
    #[Test]
    public function user_with_empty_data_cannot_join(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/auth/join', []));

        self::assertEquals(500, $response->getStatusCode());
    }

    /**
     * @throws JsonException
     */
    #[Test]
    public function user_with_invalid_email_cannot_join(): void
    {
        $response = $this->app()->handle(self::json('POST', '/v1/auth/join', [
            'email' => 'not-email',
            'password' => '',
        ]));

        self::assertEquals(500, $response->getStatusCode());
    }
}
