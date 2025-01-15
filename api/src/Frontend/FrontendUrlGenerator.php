<?php

declare(strict_types=1);

namespace App\Frontend;

final readonly class FrontendUrlGenerator
{
    public function __construct(private string $baseUrl)
    {
    }

    /** @param array<string, int|string> $params  */
    public function generate(string $uri, array $params = []): string
    {
        return $this->baseUrl
            . ($uri ? '/' . $uri : '')
            . ($params ? '?' . http_build_query($params) : '');
    }
}
