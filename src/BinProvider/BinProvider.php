<?php

namespace App\BinProvider;

use App\Http\ApiClient;
use RuntimeException;

class BinProvider implements BinProviderInterface
{
    private string $apiUrl;
    private string $apiKey;
    private ApiClient $apiClient;

    public function __construct(string $apiUrl, string $apiKey, ApiClient $apiClient)
    {
        $this->apiUrl = $apiUrl;
        $this->apiKey = $apiKey;
        $this->apiClient = $apiClient;
    }

    public function getBinDetails(string $bin): array
    {
        $url = $this->apiUrl . $bin;
        $headers = [
            'Authorization' => 'Bearer ' . $this->apiKey,
        ];

        return $this->apiClient->get($url, $headers);
    }
}
