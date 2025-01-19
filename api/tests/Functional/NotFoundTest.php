<?php

declare(strict_types=1);

namespace Test\Functional;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Exception;
use JsonException;
use PHPUnit\Framework\Attributes\Test;

/**
 * @internal
 */
final class NotFoundTest extends WebTestCase
{
    use ArraySubsetAsserts;

    /**
     * @throws JsonException
     * @throws Exception
     */
    #[Test]
    public function test_not_found(): void
    {
        $response = $this->app()->handle(self::json('GET', '/not-found'));

        self::assertEquals(404, $response->getStatusCode());
        self::assertJson($body = (string) $response->getBody());

        /** @var array $data */
        $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        self::assertArraySubset([
            'message' => '404 Not Found',
        ], $data);
    }
}
