<?php

namespace App\Entity;

class Transaction
{
    public function __construct(
        private int $bin,
        private float $amount,
        private string $currency,
        private bool $eUIssued
    ) {}

    public function getBin(): int
    {
        return $this->bin;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function isEUIssued(): bool
    {
        return $this->eUIssued;
    }
}
