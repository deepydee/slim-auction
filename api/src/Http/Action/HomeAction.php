<?php

declare(strict_types=1);

namespace App\Http\Action;

use App\Http\Http;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class HomeAction
{
    public function __construct(private ResponseFactoryInterface $factory)
    {
    }
    
    /**
     * @throws \JsonException
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->factory->createResponse();
        
        return Http::json($response, new \stdClass());
    }
}
