<?php

namespace App\Client;

use App\Exceptions\CanNotGetResponseFrom3rdParty;
use App\Exceptions\FailedToLoadSingleTransaction;
use App\Settings;

class BinLocalClient implements BinClientInterface
{
    public function getAlpha2CountryCode(int $binNumber): string
    {
        if (($handle = fopen(__DIR__ . '/../../resources/binlist-data.csv', 'r')) === false) {
            throw new CanNotGetResponseFrom3rdParty('Empty response');
        }

        $entry = null;
        while (($data = fgetcsv($handle)) !== false) {
            if ((int) $data[0] !== $binNumber) {
                continue;
            }

            $entry = $data;
        }
        fclose($handle);

        if ($entry === null) {
            throw new FailedToLoadSingleTransaction("BIN '$binNumber' not found in file");
        }

        return $entry[5];
    }

    public function isEUIssued(int $binNumber): bool
    {
        return in_array($this->getAlpha2CountryCode($binNumber), Settings::get('alpha2EUCountryCodes'));
    }
}
