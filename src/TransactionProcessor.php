<?php

namespace App;

use RuntimeException;

class TransactionProcessor
{
    private CommissionCalculator $commissionCalculator;

    public function __construct(CommissionCalculator $commissionCalculator)
    {
        $this->commissionCalculator = $commissionCalculator;
    }

    public function processFile(string $filePath): void
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $transaction = json_decode($line, true);
            if ($transaction === null) {
                throw new RuntimeException('Invalid JSON format in file.');
            }

            if (!isset($transaction['amount']) || !isset($transaction['currency']) || !isset($transaction['bin'])) {
                throw new RuntimeException('Invalid transaction data: missing amount, currency, or bin.');
            }

            try {
                $commission = $this->commissionCalculator->calculateCommission(
                    (float) $transaction['amount'],
                    $transaction['currency'],
                    $transaction['bin']
                );

                echo $commission . "\n";
            } catch (RuntimeException $e) {
                echo 'Error processing transaction: ' . $e->getMessage() . "\n";
            }
        }
    }

}