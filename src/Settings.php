<?php

namespace App;

use Exception;

class Settings
{
    private static array $values = [
        'exchangeRatesApiKey' => 'g6tgDzMK5SDJYdcv5CJ42mRCLs7RjjbE',
        'alpha2EUCountryCodes' => [
            'AT',
            'BE',
            'BG',
            'CY',
            'CZ',
            'DE',
            'DK',
            'EE',
            'ES',
            'FI',
            'FR',
            'GR',
            'HR',
            'HU',
            'IE',
            'IT',
            'LT',
            'LU',
            'LV',
            'MT',
            'NL',
            'PO',
            'PT',
            'RO',
            'SE',
            'SI',
            'SK',
        ],
        'baseCurrency' => 'EUR',
        'commissionRateEU' => 0.01,
        'commissionRateNonEU' => 0.02,
    ];

    public static function get(string $key): mixed
    {
        if (!isset(self::$values[$key])) {
            throw new Exception('Settings key "' . $key . '" does not exists.');
        }

        return self::$values[$key];
    }
}
