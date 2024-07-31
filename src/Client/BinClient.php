<?php

namespace App\Client;

use App\Settings;

class BinClient implements BinClientInterface
{
    public function __construct(private SimpleJsonHttpClient $httpClient) {}

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
