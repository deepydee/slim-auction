<?php

declare(strict_types=1);

namespace App\Http\Test\Unit\Middleware;

use App\Http\Middleware\ValidationExceptionHandler;
use App\Http\Validator\ValidationException;
use JsonException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * @internal
 */
#[CoversClass(ValidationExceptionHandler::class)]
final class ValidationExceptionHandlerTest extends TestCase
{
    /**
     * @throws Exception
     */
    #[Test]
    public function normal_request(): void
    {
        $middleware = new ValidationExceptionHandler();

        $handler = self::createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn($source = self::createResponse());

        $response = $middleware->process(self::createRequest(), $handler);

        self::assertEquals($source, $response);
    }

    /**
     * @throws Exception
     * @throws JsonException
     */
    #[Test]
    public function test_exception(): void
    {
        $middleware = new ValidationExceptionHandler();

        $violations = new ConstraintViolationList([
            new ConstraintViolation('Incorrect Email', null, [], null, 'email', 'not-email'),
            new ConstraintViolation('Empty Password', null, [], null, 'password', ''),
        ]);

        $handler = self::createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willThrowException(new ValidationException($violations));

        $response = $middleware->process(self::createRequest(), $handler);

        self::assertEquals(422, $response->getStatusCode());
        self::assertJson($body = (string) $response->getBody());

        /** @var array $data */
        $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals([
            'errors' => [
                'email' => 'Incorrect Email',
                'password' => 'Empty Password',
            ],
        ], $data);
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
