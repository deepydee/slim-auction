<?php

declare(strict_types=1);

namespace App\Http;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\StreamInterface;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Interfaces\HeadersInterface;
use Slim\Psr7\Response;

final class JsonResponse extends Response
{
    /**
     * @param  array|object  $data
     * @param  int  $status
     *
     * @throws \JsonException
     */
    public function __construct(
        array|object $data = [],
        int $status = StatusCodeInterface::STATUS_OK,
    ) {
        parent::__construct(
            status: $status,
            headers: new Headers(['Content-Type' => 'application/json']),
            body: (new StreamFactory())->createStream(json_encode($data, JSON_THROW_ON_ERROR))
        );
    }
}