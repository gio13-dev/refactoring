<?php

namespace App\Tests;

use App\BinProvider\BinProviderInterface;
use App\CommissionCalculator;
use App\ExchangeRateProvider\ExchangeRateProviderInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class CommissionCalculatorTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCalculateCommissionForEuCountry()
    {
        // Mock BinProviderInterface
        $binProvider = $this->createMock(BinProviderInterface::class);
        $binProvider->method('getBinDetails')
            ->willReturn([
                'country' => [
                    'alpha2' => 'DE' // Germany is in the EU
                ]
            ]);

        // Mock ExchangeRateProviderInterface
        $exchangeRateProvider = $this->createMock(ExchangeRateProviderInterface::class);
        $exchangeRateProvider->method('getExchangeRate')
            ->willReturn(1.0); // 1 EUR = 1 EUR

        $calculator = new CommissionCalculator($binProvider, $exchangeRateProvider);
        $commission = $calculator->calculateCommission(100.0, 'EUR', '123456');

        $this->assertEquals(1.0, $commission); // 1% of 100 EUR
    }

    /**
     * @throws Exception
     */
    public function testCalculateCommissionForNonEuCountry()
    {
        // Mock BinProviderInterface
        $binProvider = $this->createMock(BinProviderInterface::class);
        $binProvider->method('getBinDetails')
            ->willReturn([
                'country' => [
                    'alpha2' => 'US' // US is not in the EU
                ]
            ]);

        // Mock ExchangeRateProviderInterface
        $exchangeRateProvider = $this->createMock(ExchangeRateProviderInterface::class);
        $exchangeRateProvider->method('getExchangeRate')
            ->willReturn(1.2); // 1 EUR = 1.2 USD

        $calculator = new CommissionCalculator($binProvider, $exchangeRateProvider);
        $commission = $calculator->calculateCommission(120.0, 'USD', '123456');

        $this->assertEquals(2.0, $commission); // 2% of 100 EUR (120 USD / 1.2)
    }

    /**
     * @throws Exception
     */
    public function testCalculateCommissionWithCeiling()
    {
        // Mock BinProviderInterface
        $binProvider = $this->createMock(BinProviderInterface::class);
        $binProvider->method('getBinDetails')
            ->willReturn([
                'country' => [
                    'alpha2' => 'DE'
                ]
            ]);

        // Mock ExchangeRateProviderInterface
        $exchangeRateProvider = $this->createMock(ExchangeRateProviderInterface::class);
        $exchangeRateProvider->method('getExchangeRate')
            ->willReturn(1.0);

        $calculator = new CommissionCalculator($binProvider, $exchangeRateProvider);

        // Test rounding up to the nearest cent
        $commission = $calculator->calculateCommission(100.4618, 'EUR', '123456');
        $this->assertEquals(1.01, $commission);

        $commission = $calculator->calculateCommission(100.4600, 'EUR', '123456');
        $this->assertEquals(1.01, $commission);
    }
}