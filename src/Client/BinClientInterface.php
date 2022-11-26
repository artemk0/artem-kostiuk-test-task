<?php

declare(strict_types=1);

namespace App\Client;

interface BinClientInterface
{
    public function __construct(SimpleJsonHttpClient $httpClient);

    public function getAlpha2CountryCode(int $binNumber): string;

    public function isEUIssued(int $binNumber): bool;
}
