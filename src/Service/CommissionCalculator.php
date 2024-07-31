<?php

namespace App\Service;

use App\Client\ExchangeRatesClient;
use App\Settings;

class CommissionCalculator
{
    public function __construct(
        private array $transactions,
        private ExchangeRatesClient $exchangeRatesClient,
        private array $commissions = []
    ) {}

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

    public function getCommissions(): array
    {
        return $this->commissions;
    }
}
