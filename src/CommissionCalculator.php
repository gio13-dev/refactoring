<?php

namespace App;

use App\BinProvider\BinProviderInterface;
use App\ExchangeRateProvider\ExchangeRateProviderInterface;
use RuntimeException;

class CommissionCalculator
{
    private const array EU_COUNTRIES = [
        'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PO', 'PT', 'RO', 'SE', 'SI', 'SK'
    ];

    private BinProviderInterface $binProvider;
    private ExchangeRateProviderInterface $exchangeRateProvider;

    public function __construct(BinProviderInterface $binProvider, ExchangeRateProviderInterface $exchangeRateProvider)
    {
        $this->binProvider = $binProvider;
        $this->exchangeRateProvider = $exchangeRateProvider;
    }

    public function calculateCommission(float $amount, string $currency, string $bin): float
    {
        $binDetails = $this->binProvider->getBinDetails($bin);
        $countryCode = $binDetails['country']['alpha2'] ?? null;

        if ($countryCode === null) {
            throw new RuntimeException('Invalid BIN details: Missing country code.');
        }

        $isEu = $this->isEu($countryCode);
        $rate = $this->exchangeRateProvider->getExchangeRate($currency);
        $amountFixed = $this->convertToEur($amount, $currency, $rate);

        $commission = $amountFixed * ($isEu ? 0.01 : 0.02);

        return $this->ceilCents($commission);
    }

    private function isEu(string $countryCode): bool
    {
        return in_array($countryCode, self::EU_COUNTRIES);
    }

    private function convertToEur(float $amount, string $currency, float $rate): float
    {
        if ($currency === 'EUR' || $rate == 0) {
            return $amount;
        }
        return $amount / $rate;
    }

    private function ceilCents(float $amount): float
    {
        return ceil($amount * 100) / 100;
    }
}
