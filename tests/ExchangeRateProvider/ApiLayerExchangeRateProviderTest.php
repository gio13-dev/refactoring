<?php

namespace App\Tests\ExchangeRateProvider;

use App\ExchangeRateProvider\ApiLayerExchangeRateProvider;
use App\Http\ApiClient;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;


class ApiLayerExchangeRateProviderTest extends TestCase
{
    public function testGetExchangeRateSuccess()
    {
        // Mock Guzzle response
        $mockResponse = json_encode([
            'rates' => [
                'USD' => 1.2
            ]
        ]);

        $mock = new MockHandler([
            new Response(200, [], $mockResponse),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $httpClient = new Client(['handler' => $handlerStack]);

        $apiClient = new ApiClient($httpClient);
        $provider = new ApiLayerExchangeRateProvider('https://api.apilayer.com/exchangerates_data/latest', 'api-key', $apiClient);

        $rate = $provider->getExchangeRate('USD');
        $this->assertEquals(1.2, $rate);
    }

    public function testGetExchangeRateFailure()
    {
        // Mock Guzzle exception
        $mock = new MockHandler([
            new RequestException('Error', new Request('GET', 'test')),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $httpClient = new Client(['handler' => $handlerStack]);

        $apiClient = new ApiClient($httpClient);
        $provider = new ApiLayerExchangeRateProvider('https://api.apilayer.com/exchangerates_data/latest', 'api-key', $apiClient);

        $this->expectException(RuntimeException::class);
        $provider->getExchangeRate('USD');
    }
}