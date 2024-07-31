<?php

namespace App\Client;

use App\Exceptions\CanNotGetResponseFrom3rdParty;
use App\Settings;

class ExchangeRatesClient implements ExchangeRatesClientInterface
{
    public function __construct(
        private SimpleJsonHttpClient $httpClient,
        private ?array $rates = null
    ) {}

    public function getRates(): array
    {
        $streamContextOptions = [
            'http' => [
                'header' => 'apikey: ' . Settings::get('exchangeRatesApiKey')
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ];

        $response = $this->httpClient->sendRequest('https://api.apilayer.com/exchangerates_data/latest', $streamContextOptions);

        return $response['rates'];
    }

    public function getRate(string $currency): float
    {
        if (!isset($this->rates)) {
            $this->rates = $this->getRates();
        }

        if (!isset($this->rates[$currency])) {
            throw new CanNotGetResponseFrom3rdParty('Currency "' . $currency . '" does not have a rate.');
        }

        return (float)$this->rates[$currency];
    }
}
