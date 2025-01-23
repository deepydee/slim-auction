<?php

declare(strict_types=1);

namespace App\Http\Test\Unit\Middleware;

use App\Http\Middleware\TranslatorLocale;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Symfony\Component\Translation\Translator;

/**
 * @internal
 */
#[CoversClass(TranslatorLocale::class)]
final class TranslatorLocaleTest extends TestCase
{
    /**
     * @throws Exception
     */
    #[Test]
    public function test_default(): void
    {
        $translator = $this->createMock(Translator::class);
        $translator->expects(self::once())
            ->method('setLocale')
            ->with(
                self::equalTo('en')
            );

        $middleware = new TranslatorLocale($translator, ['en', 'ru']);

        $handler = self::createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn($source = self::createResponse());

        $response = $middleware->process(self::createRequest(), $handler);

        self::assertEquals($source, $response);
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function test_accepted(): void
    {
        $translator = $this->createMock(Translator::class);
        $translator->expects(self::once())
            ->method('setLocale')
            ->with(
                self::equalTo('ru')
            );

        $middleware = new TranslatorLocale($translator, ['en', 'ru']);

        $handler = self::createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(self::createResponse());

        $request = self::createRequest()->withHeader('Accept-Language', 'ru');

        $middleware->process($request, $handler);
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function test_multi(): void
    {
        $translator = $this->createMock(Translator::class);
        $translator->expects(self::once())
            ->method('setLocale')
            ->with(
                self::equalTo('ru')
            );

        $middleware = new TranslatorLocale($translator, ['en', 'fr', 'ru']);

        $handler = self::createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(self::createResponse());

        $request = self::createRequest()->withHeader('Accept-Language', 'es;q=0.9, ru;q=0.8, *;q=0.5');

        $middleware->process($request, $handler);
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function test_other(): void
    {
        $translator = $this->createMock(Translator::class);
        $translator->expects(self::once())
            ->method('setLocale')
            ->with(
                self::equalTo('en')
            );

        $middleware = new TranslatorLocale($translator, ['en', 'ru']);

        $handler = self::createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(self::createResponse());

        $request = self::createRequest()->withHeader('Accept-Language', 'es');

        $middleware->process($request, $handler);
    }

    private static function createRequest(): ServerRequestInterface
    {
        return (new ServerRequestFactory())->createServerRequest('POST', 'http://test');
    }

    private static function createResponse(): ResponseInterface
    {
        return (new ResponseFactory())->createResponse();
    }
}
