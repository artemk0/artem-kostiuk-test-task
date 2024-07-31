<?php

namespace App\Client;

interface BinClientInterface
{
    public function getAlpha2CountryCode(int $binNumber): string;

    public function isEUIssued(int $binNumber): bool;
}
