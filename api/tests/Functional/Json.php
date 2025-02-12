<?php

declare(strict_types=1);

namespace Test\Functional;

use JsonException;

final class Json
{
    /**
     * @throws JsonException
     * @return array<array-key, mixed>
     */
    public static function decode(string $data): array
    {
        /** @var array<string, mixed> */
        return json_decode($data, true, 512, JSON_THROW_ON_ERROR);
    }
}
