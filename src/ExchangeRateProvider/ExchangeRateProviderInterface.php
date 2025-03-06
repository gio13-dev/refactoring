<?php

namespace App\ExchangeRateProvider;

interface ExchangeRateProviderInterface
{
    public function getExchangeRate(string $currency): float;
}
