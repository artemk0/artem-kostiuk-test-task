<?php

namespace tests\Service;

use App\Client\ExchangeRatesClient;
use App\Entity\Transaction;
use App\Service\CommissionCalculator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CommissionCalculatorTest extends TestCase
{
    #[DataProvider('calcCommissionsProvider')]
    public function testCalcCommissions(int $binNumber, array $transactions, string $currency, array $rates, float $expected): void
    {
        $exchangeRatesClientMock = $this->getMockBuilder(ExchangeRatesClient::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getRate'])
            ->getMock()
        ;

        $exchangeRatesClientMock
            ->method('getRate')
            ->willReturn($rates[$currency])
        ;

        $commissions = (new CommissionCalculator($transactions, $exchangeRatesClientMock))
            ->calcCommissions()
            ->getCommissions()
        ;

        foreach ($commissions as $commission) {
            if ($commission['transaction']->getCurrency() !== $currency || $commission['transaction']->getBin() !== $binNumber) {
                continue;
            }

            $this->assertEquals($expected, $commission['value']);
        }
    }

    public static function calcCommissionsProvider(): iterable
    {
        $transactions = [
            new Transaction(45717360,100.00,'EUR', true),
            new Transaction(516793,50.00,'USD', true),
            new Transaction(45417360,10000.00,'JPY', false),
            new Transaction(41417360, 130.00, 'USD', false),
            new Transaction(4745030, 2000.00, 'GBP', false),
        ];

        $rates = [
            'EUR' => 1.0,
            'JPY' => 144.891111,
            'USD' => 1.041445,
            'GBP' => 0.861339,
        ];

        yield [45717360, $transactions, 'EUR', $rates, 1.0];
        yield [45417360, $transactions, 'JPY', $rates, 1.38];
        yield [4745030, $transactions, 'GBP', $rates, 46.44];
        yield [516793, $transactions, 'USD', $rates, 0.48];
        yield [41417360, $transactions, 'USD', $rates, 2.5];
    }
}
