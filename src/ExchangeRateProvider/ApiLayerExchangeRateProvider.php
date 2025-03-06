<?php

namespace App\ExchangeRateProvider;

use App\Http\ApiClient;
use RuntimeException;

class ApiLayerExchangeRateProvider implements ExchangeRateProviderInterface
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

    public function getExchangeRate(string $currency): float
    {
        if ($currency === 'EUR') {
            return 1.0;
        }

        $headers = [
            'apikey' => $this->apiKey,
        ];

        $rates = $this->apiClient->get($this->apiUrl, $headers);

        if (isset($rates['error'])) {
            throw new RuntimeException('API Error: ' . $rates['error']['message']);
        }

        if (!isset($rates['rates'][$currency])) {
            throw new RuntimeException("Currency '$currency' not found in exchange rates.");
        }

        return $rates['rates'][$currency];
    }
}