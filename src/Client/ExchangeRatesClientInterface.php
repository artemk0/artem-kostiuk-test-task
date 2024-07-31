<?php

namespace App\Client;

interface ExchangeRatesClientInterface
{
    public function __construct(SimpleJsonHttpClient $httpClient, ?array $rates);

    public function getRates(): array;

    public function getRate(string $currency): float;
}
