<?php

declare(strict_types=1);

namespace App\Http\Action;

use App\Http\Http;
use App\Http\JsonResponse;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class HomeAction
{
    /**
     * @throws \JsonException
     */
    public function __invoke(): ResponseInterface
    {
        return new JsonResponse(data: new \stdClass());
    }
}
