<?php

declare(strict_types=1);

namespace Test\Unit\Http;

use App\Http\JsonResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(JsonResponse::class)]
final class JsonResponseTest extends TestCase
{
    /**
     * @throws \JsonException
     */
    #[Test]
    #[Group('api')]
    public function it_can_test_with_code(): void
    {
        $response = new JsonResponse(0, 201);

        self::assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        self::assertEquals('0', $response->getBody()->getContents());
        self::assertEquals(201, $response->getStatusCode());
    }

    /**
     * @throws \JsonException
     */
    #[Test]
    #[Group('api')]
    public function it_can_test_null(): void
    {
        $response = new JsonResponse(null);

        self::assertEquals('null', $response->getBody()->getContents());
        self::assertEquals(200, $response->getStatusCode());
    }

    /**
     * @throws \JsonException
     */
    #[Test]
    #[Group('api')]
    public function it_can_test_int(): void
    {
        $response = new JsonResponse(12);

        self::assertEquals('12', $response->getBody()->getContents());
        self::assertEquals(200, $response->getStatusCode());
    }

    #[Test]
    #[Group('api')]
    public function it_can_test_string(): void
    {
        $response = new JsonResponse('12');

        self::assertEquals('"12"', $response->getBody()->getContents());
        self::assertEquals(200, $response->getStatusCode());
    }

    /**
     * @throws \JsonException
     */
    #[Test]
    #[Group('api')]
    public function it_can_test_object(): void
    {
        $object = new \stdClass();
        $object->str = 'value';
        $object->int = 1;
        $object->none = null;

        $response = new JsonResponse($object);

        self::assertEquals('{"str":"value","int":1,"none":null}', $response->getBody()->getContents());
        self::assertEquals(200, $response->getStatusCode());
    }

    /**
     * @throws \JsonException
     */
    #[Test]
    #[Group('api')]
    public function it_can_test_array(): void
    {
        $array = ['str' => 'value', 'int' => 1, 'none' => null];

        $response = new JsonResponse($array);

        self::assertEquals('{"str":"value","int":1,"none":null}', $response->getBody()->getContents());
        self::assertEquals(200, $response->getStatusCode());
    }
}
