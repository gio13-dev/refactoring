<?php

namespace App\Http;

use App\Utils\JsonUtils;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;

class ApiClient
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function get(string $url, array $headers = []): array
    {
        try {
            $response = $this->client->get($url, [
                'headers' => $headers,
            ]);

            return JsonUtils::decode($response->getBody()->getContents());
        } catch (GuzzleException $e) {
            throw new RuntimeException('API request failed: ' . $e->getMessage());
        }
    }
}