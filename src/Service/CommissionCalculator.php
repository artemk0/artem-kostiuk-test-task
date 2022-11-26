<?php

declare(strict_types=1);

namespace App\Service;

use App\Client\ExchangeRatesClient;
use App\Settings;

class CommissionCalculator
{
    /**
     * @var array
     */
    private array $transactions;

    /**
     * @var ExchangeRatesClient
     */
    private ExchangeRatesClient $exchangeRatesClient;

    /**
     * @var array
     */
    private array $commissions;

    public function __construct(array $transactions, ExchangeRatesClient $exchangeRatesClient)
    {
        $this->transactions = $transactions;
        $this->exchangeRatesClient = $exchangeRatesClient;
    }

    public function calcCommissions(): self
    {
        foreach ($this->transactions as $transaction) {
            $currency = $transaction->getCurrency();
            $rate = $this->exchangeRatesClient->getRate($currency);

            $needsConversion = $currency !== Settings::get('baseCurrency') || $rate > 0;

            $amount = $transaction->getAmount();
            if ($needsConversion) {
                $amount = $amount / $rate;
            }

            $commissionRate = $transaction->isEUIssued()
                ? Settings::get('commissionRateEU')
                : Settings::get('commissionRateNonEU');

            $this->commissions[] = [
                'value' => round($amount * $commissionRate, 2),
                'transaction' => $transaction,
            ];
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getCommissions(): array
    {
        return $this->commissions;
    }
}
