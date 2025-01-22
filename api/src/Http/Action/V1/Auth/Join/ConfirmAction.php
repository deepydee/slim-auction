<?php

declare(strict_types=1);

namespace App\Http\Action\V1\Auth\Join;

use App\Auth\Command\JoinByEmail\Confirm\Command;
use App\Auth\Command\JoinByEmail\Confirm\Handler;
use App\Http\EmptyResponse;
use App\Http\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class ConfirmAction implements RequestHandlerInterface
{
    public function __construct(
        private Handler $handler,
        private ValidatorInterface $validator,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /**
         * @var array{token: ?string} $data
         */
        $data = $request->getParsedBody();

        $command = new Command(token: $data['token'] ?? '');

        $violations = $this->validator->validate($command);
        if ($violations->count() > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            return new JsonResponse(['errors' => $errors], 422);
        }

        $this->handler->handle($command);

        return new EmptyResponse(200);
    }
}
