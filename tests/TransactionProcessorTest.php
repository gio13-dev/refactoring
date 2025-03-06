<?php

namespace App\Tests;

use App\CommissionCalculator;
use App\TransactionProcessor;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class TransactionProcessorTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testProcessFile()
    {
        // Mock CommissionCalculator
        $calculator = $this->createMock(CommissionCalculator::class);
        $calculator->method('calculateCommission')
            ->willReturn(1.0);

        // Create a temporary file with test data
        $filePath = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($filePath, json_encode([
            'bin' => '123456',
            'amount' => 100.0,
            'currency' => 'EUR'
        ]));

        $processor = new TransactionProcessor($calculator);
        $this->expectOutputString("1\n"); // Expected output
        $processor->processFile($filePath);

        // Clean up
        unlink($filePath);
    }

    /**
     * @throws Exception
     */
    public function testProcessFileWithInvalidJson()
    {
        // Mock CommissionCalculator
        $calculator = $this->createMock(CommissionCalculator::class);

        // Create a temporary file with invalid JSON
        $filePath = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($filePath, 'invalid-json');

        $processor = new TransactionProcessor($calculator);

        $this->expectException(\RuntimeException::class);
        $processor->processFile($filePath);

        // Clean up
        unlink($filePath);
    }

    /**
     * @throws Exception
     */
    public function testProcessFileWithMissingFields()
    {
        // Mock CommissionCalculator
        $calculator = $this->createMock(CommissionCalculator::class);

        // Create a temporary file with missing fields
        $filePath = tempnam(sys_get_temp_dir(), 'test');

        if ($filePath === false) {
            $this->fail('Failed to create a temporary file.');
        }

        file_put_contents($filePath, json_encode([
            ['bin' => '123456'] // Missing 'amount' and 'currency'
        ]));

        $processor = new TransactionProcessor($calculator);

        $this->expectException(RuntimeException::class);

        try {
            $processor->processFile($filePath);
        } finally {
            // Ensure file is deleted even if the test fails
            unlink($filePath);
        }
    }
}