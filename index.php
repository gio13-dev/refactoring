<?php

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use App\Container\Container;
use App\Config\Config;
use App\Http\ApiClient;
use App\BinProvider\BinProvider;
use App\ExchangeRateProvider\ApiLayerExchangeRateProvider;
use App\CommissionCalculator;
use App\TransactionProcessor;
use GuzzleHttp\Client;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$config = new Config(__DIR__ . '/config.php');

$container = new Container();

$container->set('http.client', function () {
    return new Client();
});

$container->set('http.api_client', function (Container $container) {
    return new ApiClient($container->get('http.client'));
});

$container->set('bin_provider', function (Container $container) use ($config) {
    $apiUrl = $config->get('bin_lookup.url');
    $apiKey = $config->get('bin_lookup.api_key');

    return new BinProvider(
        $apiUrl,
        $apiKey,
        $container->get('http.api_client')
    );
});

$container->set('exchange_rate_provider', function (Container $container) use ($config) {
    return new ApiLayerExchangeRateProvider(
        $config->get('exchange_rates.url'),
        $config->get('exchange_rates.api_key'),
        $container->get('http.api_client')
    );
});

$container->set('commission_calculator', function (Container $container) {
    return new CommissionCalculator(
        $container->get('bin_provider'),
        $container->get('exchange_rate_provider')
    );
});


$container->set('transaction_processor', function (Container $container) {
    return new TransactionProcessor(
        $container->get('commission_calculator')
    );
});

if ($argc < 2) {
    echo "Usage: php index.php <input_file>\n";
    exit(1);
}

$processor = $container->get('transaction_processor');
$processor->processFile($argv[1]);
