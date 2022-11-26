<?php

declare(strict_types=1);

namespace App\Client;

interface ExchangeRatesClientInterface
{
    public function __construct(SimpleJsonHttpClient $httpClient);

    public function getRates(): array;

    public function getRate(string $currency): float;
}
