<?php

declare(strict_types=1);

use App\Client\ExchangeRatesClient;
use App\Entity\Transaction;
use App\Service\CommissionCalculator;
use PHPUnit\Framework\TestCase;

class CommissionCalculatorTest extends TestCase
{
    /**
     * @dataProvider calcCommissionsProvider
     */
    public function testCalcCommissions(int $binNumber, array $transactions, string $currency, array $rates, float $expected)
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

    public function calcCommissionsProvider(): array
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

        return [
            [45717360, $transactions, 'EUR', $rates, 1.0],
            [45417360, $transactions, 'JPY', $rates, 1.38],
            [4745030, $transactions, 'GBP', $rates, 46.44],
            [516793, $transactions, 'USD', $rates, 0.48],
            [41417360, $transactions, 'USD', $rates, 2.5],
        ];
    }
}
