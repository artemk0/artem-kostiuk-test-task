<?php

declare(strict_types=1);

namespace App\Client;

use App\Exceptions\CanNotGetResponseFrom3rdParty;
use App\Settings;

class ExchangeRatesClient implements ExchangeRatesClientInterface
{
    private SimpleJsonHttpClient $httpClient;

    private ?array $rates;

    public function __construct(SimpleJsonHttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getRates(): array
    {
        $this->rates = null;

        $streamContextOptions = [
            'http' => [
                'header' => 'apikey: ' . Settings::get('exchangeRatesApiKey')
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]
        ];

        $response = $this->httpClient->sendRequest('https://api.apilayer.com/exchangerates_data/latest', $streamContextOptions);

        return $response['rates'];
    }

    public function getRate(string $currency): float
    {
        if (empty($this->rates)) {
            $this->rates = $this->getRates();
        }

        if (empty($this->rates[$currency])) {
            throw new CanNotGetResponseFrom3rdParty('Currency "' . $currency . '" does not have a rate.');
        }

        return (float)$this->rates[$currency];
    }
}
