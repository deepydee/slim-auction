<?php

declare(strict_types=1);

namespace App\Http\Action;

use App\Http\JsonResponse;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use stdClass;

final readonly class HomeAction
{
    /**
     * @throws JsonException
     */
    public function __invoke(): ResponseInterface
    {
        return new JsonResponse(data: new stdClass());
    }
}
