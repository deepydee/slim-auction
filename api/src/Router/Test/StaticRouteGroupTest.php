<?php

declare(strict_types=1);

namespace App\Router\Test;

use App\Router\StaticRouteGroup;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Slim\Routing\RouteCollectorProxy;

/**
 * @internal
 */
#[CoversClass(StaticRouteGroup::class)]
final class StaticRouteGroupTest extends TestCase
{
    /**
     * @throws Exception
     */
    #[Test]
    public function it_can_use_a_callable(): void
    {
        $collector = self::createStub(RouteCollectorProxy::class);

        $callable = static fn (RouteCollectorProxy $collector): RouteCollectorProxy => $collector;

        $group = new StaticRouteGroup($callable);

        self::assertSame($collector, $group($collector));
    }
}
