<?php

declare(strict_types=1);

namespace App\Http\Action;

use App\Http\Http;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final readonly class HomeAction
{
    /**
     * @throws \JsonException
     */
    public function __invoke(Request $request, Response $response, $args): Response
    {
        return HTTP::json($response, new \StdClass());
    }
}
