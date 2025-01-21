<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Auth\Join;

use App\Auth\Command\JoinByEmail\Confirm\Command;
use App\Auth\Command\JoinByEmail\Confirm\Handler;
use App\Http\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final readonly class ConfirmAction implements RequestHandlerInterface
{
    public function __construct(private Handler $handler)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /**
         * @var array{token: ?string} $data
         */
        $data = $request->getParsedBody();

        $command = new Command(token: $data['token'] ?? '');

        $this->handler->handle($command);

        return new EmptyResponse(200);
    }
}
