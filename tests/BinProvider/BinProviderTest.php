<?php

namespace App\Tests\BinProvider;

use App\BinProvider\BinProvider;
use App\Http\ApiClient;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

class BinProviderTest extends TestCase
{
    public function testGetBinDetailsSuccess()
    {
        // Mock Guzzle response
        $mockResponse = json_encode([
            'country' => [
                'alpha2' => 'DE'
            ]
        ]);

        $mock = new MockHandler([
            new Response(200, [], $mockResponse),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $httpClient = new Client(['handler' => $handlerStack]);

        $apiClient = new ApiClient($httpClient);
        $provider = new BinProvider('https://lookup.binlist.net/', 'api-key', $apiClient);
        $result = $provider->getBinDetails('123456');

        $this->assertEquals('DE', $result['country']['alpha2']);
    }

    public function testGetBinDetailsFailure()
    {
        // Mock Guzzle exception
        $mock = new MockHandler([
            new RequestException('Error', new Request('GET', 'test')),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $httpClient = new Client(['handler' => $handlerStack]);

        $apiClient = new ApiClient($httpClient);
        $provider = new BinProvider('https://lookup.binlist.net/', 'api-key', $apiClient);

        $this->expectException(RuntimeException::class);
        $provider->getBinDetails('123456');
    }
}