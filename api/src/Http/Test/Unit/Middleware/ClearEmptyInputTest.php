<?php

declare(strict_types=1);

namespace App\Http\Test\Unit\Middleware;

use App\Http\Middleware\ClearEmptyInput;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Factory\UploadedFileFactory;

/**
 * @internal
 */
#[CoversClass(ClearEmptyInput::class)]
final class ClearEmptyInputTest extends TestCase
{
    /**
     * @throws Exception
     */
    #[Test]
    public function it_can_clear_request_body_values(): void
    {
        $middleware = new ClearEmptyInput();

        $request = (new ServerRequestFactory())->createServerRequest('POST', 'http://test')
            ->withParsedBody([
                'null' => null,
                'space' => ' ',
                'string' => 'String ',
                'nested' => [
                    'null' => null,
                    'space' => ' ',
                    'name' => ' Name',
                ],
            ]);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects(self::once())->method('handle')
            ->willReturnCallback(static function (ServerRequestInterface $request): ResponseInterface {
                self::assertEquals([
                    'null' => null,
                    'space' => '',
                    'string' => 'String',
                    'nested' => [
                        'null' => null,
                        'space' => '',
                        'name' => 'Name',
                    ],
                ], $request->getParsedBody());

                return (new ResponseFactory())->createResponse();
            });

        $middleware->process($request, $handler);
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function test_uploaded_files(): void
    {
        $middleware = new ClearEmptyInput();

        $realFile = (new UploadedFileFactory())->createUploadedFile(
            (new StreamFactory())->createStream(''),
            0,
            UPLOAD_ERR_OK,
        );

        $noFile = (new UploadedFileFactory())->createUploadedFile(
            (new StreamFactory())->createStream(''),
            0,
            UPLOAD_ERR_NO_FILE,
        );

        $request = (new ServerRequestFactory())->createServerRequest('POST', 'http://test')
            ->withUploadedFiles([
                'real_file' => $realFile,
                'none_file' => $noFile,
                'files' => [$realFile, $noFile],
            ]);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects(self::once())->method('handle')
            ->willReturnCallback(static function (ServerRequestInterface $request) use ($realFile): ResponseInterface {
                self::assertEquals([
                    'real_file' => $realFile,
                    'files' => [$realFile],
                ], $request->getUploadedFiles());

                return (new ResponseFactory())->createResponse();
            });

        $middleware->process($request, $handler);
    }
}
