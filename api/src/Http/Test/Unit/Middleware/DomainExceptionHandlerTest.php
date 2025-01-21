<?php

declare(strict_types=1);

namespace App\Http\Test\Unit\Middleware;

use App\Http\Middleware\DomainExceptionHandler;
use DomainException;
use JsonException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

/**
 * @internal
 */
#[CoversClass(DomainExceptionHandler::class)]
final class DomainExceptionHandlerTest extends TestCase
{
    /**
     * @throws Exception
     */
    #[Test]
    public function domain_exception_is_not_handled(): void
    {
        $middleware = new DomainExceptionHandler();

        $handler = self::createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn($source = (new ResponseFactory())->createResponse());

        $request = (new ServerRequestFactory())->createServerRequest('POST', 'http://test');
        $response = $middleware->process($request, $handler);

        self::assertEquals($source, $response);
    }

    /**
     * @throws Exception
     * @throws JsonException
     */
    #[Test]
    public function domain_exception_is_handled_correctly(): void
    {
        $middleware = new DomainExceptionHandler();

        $handler = self::createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willThrowException(new DomainException('Some error.'));

        $request = (new ServerRequestFactory())->createServerRequest('POST', 'http://test');
        $response = $middleware->process($request, $handler);

        self::assertEquals(409, $response->getStatusCode());
        self::assertJson($body = (string) $response->getBody());

        /** @var array $data */
        $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals([
            'message' => 'Some error.',
        ], $data);
    }
}
