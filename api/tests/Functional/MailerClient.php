<?php

declare(strict_types=1);

namespace Test\Functional;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;

final class MailerClient
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'http://mailhog:8025',
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function clear(): void
    {
        $this->client->delete('/api/v1/messages');
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function hasEmailSentTo(string $to): bool
    {
        $response = $this->client->get('/api/v2/search?kind=to&query=' . urlencode($to));
        /** @psalm-var array{total:int} $data */
        $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return $data['total'] > 0;
    }
}
