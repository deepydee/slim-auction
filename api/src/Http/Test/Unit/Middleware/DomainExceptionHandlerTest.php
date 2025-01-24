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
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Symfony\Contracts\Translation\TranslatorInterface;

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
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::never())->method('warning');

        $translator = self::createStub(TranslatorInterface::class);

        $middleware = new DomainExceptionHandler($logger, $translator);

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
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())->method('warning');

        $translator = $this->createMock(TranslatorInterface::class);
        $translator
            ->expects(self::once())
            ->method('trans')
            ->with(
                self::equalTo('Some error.'),
                self::equalTo([]),
                self::equalTo('exceptions')
            )
            ->willReturn('Ошибка.');

        $middleware = new DomainExceptionHandler($logger, $translator);

        $handler = self::createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willThrowException(new DomainException('Some error.'));

        $request = (new ServerRequestFactory())->createServerRequest('POST', 'http://test');
        $response = $middleware->process($request, $handler);

        self::assertEquals(409, $response->getStatusCode());
        self::assertJson($body = (string) $response->getBody());

        /** @var array $data */
        $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals([
            'message' => 'Ошибка.',
        ], $data);
    }
}
