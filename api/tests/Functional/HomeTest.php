<?php

declare(strict_types=1);

namespace Test\Functional;

use App\Http\Action\HomeAction;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(HomeAction::class)]
final class HomeTest extends WebTestCase
{
    #[Test]
    public function it_can_success(): void
    {
        $response = $this->app()->handle(self::json('GET', '/'));

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        self::assertEquals('{}', (string) $response->getBody());
    }

    #[Test]
    public function it_can_fail(): void
    {
        $response = $this->app()->handle(self::json('POST', '/'));

        self::assertEquals(405, $response->getStatusCode());
    }

    #[Test]
    public function it_can_not_found(): void
    {
        $response = $this->app()->handle(self::json('GET', '/not-found'));

        self::assertEquals(404, $response->getStatusCode());
    }
}
