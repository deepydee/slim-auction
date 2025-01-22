<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Auth\Join;

use App\Auth\Command\JoinByEmail\Confirm\Command;
use App\Auth\Command\JoinByEmail\Confirm\Handler;
use App\Http\EmptyResponse;
use App\Http\JsonResponse;
use App\Http\Validator\ValidationException;
use App\Http\Validator\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final readonly class ConfirmAction implements RequestHandlerInterface
{
    public function __construct(
        private Handler $handler,
        private Validator $validator,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /**
         * @var array{token: ?string} $data
         */
        $data = $request->getParsedBody();

        $command = new Command(token: $data['token'] ?? '');

        try {
            $this->validator->validate($command);
        } catch (ValidationException $exception) {
            $errors = [];
            foreach ($exception->getViolations() as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            return new JsonResponse(['errors' => $errors], 422);
        }

        $this->handler->handle($command);

        return new EmptyResponse(200);
    }
}
