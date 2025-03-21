<?php

declare(strict_types=1);

namespace App\Http;

use Fig\Http\Message\StatusCodeInterface;
use JsonException;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Response;

final class JsonResponse extends Response
{
    /**
     * @throws JsonException
     */
    public function __construct(
        mixed $data,
        int $status = StatusCodeInterface::STATUS_OK,
    ) {
        parent::__construct(
            status: $status,
            headers: new Headers(['Content-Type' => 'application/json']),
            body: (new StreamFactory())->createStream(json_encode($data, JSON_THROW_ON_ERROR))
        );
    }
}
