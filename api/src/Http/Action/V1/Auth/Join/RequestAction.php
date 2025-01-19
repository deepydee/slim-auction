<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Auth\Join;

use App\Auth\Command\JoinByEmail\Request\Command;
use App\Auth\Command\JoinByEmail\Request\Handler;
use App\Http\JsonResponse;
use DomainException;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use stdClass;

final readonly class RequestAction implements RequestHandlerInterface
{
    public function __construct(private Handler $handler)
    {
    }

    /**
     * @throws JsonException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /**
         * @var array{email: ?string, password: ?string} $data
         */
        $data = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);

        $command = new Command(
            email: trim($data['email'] ?? ''),
            password: trim($data['password'] ?? ''),
        );

        try {
            $this->handler->handle($command);

            return new JsonResponse(new stdClass(), 201);
        } catch (DomainException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 409);
        }
    }
}
