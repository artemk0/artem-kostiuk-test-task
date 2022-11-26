<?php

declare(strict_types=1);

namespace App\Client;

use App\Settings;

class BinClient implements BinClientInterface
{
    private SimpleJsonHttpClient $httpClient;

    public function __construct(SimpleJsonHttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getAlpha2CountryCode(int $binNumber): string
    {
        $response = $this->httpClient->sendRequest('https://lookup.binlist.net/' . $binNumber);

        return $response['country']['alpha2'];
    }

    public function isEUIssued(int $binNumber): bool
    {
        return in_array($this->getAlpha2CountryCode($binNumber), Settings::get('alpha2EUCountryCodes'));
    }
}
