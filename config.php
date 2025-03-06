<?php

return [
    'bin_lookup' => [
        'url' => $_ENV['BIN_LOOKUP_URL'] ?? 'https://lookup.binlist.net/',
        'api_key' => $_ENV['BIN_LOOKUP_API_KEY'] ?? '',
    ],
    'exchange_rates' => [
        'url' => $_ENV['EXCHANGE_RATES_URL'] ?? 'https://api.apilayer.com/exchangerates_data/latest',
        'api_key' => $_ENV['EXCHANGE_RATES_API_KEY'] ?? '',
    ],
];