<?php

declare(strict_types=1);

namespace App\Entity;

class Transaction
{
    /**
     * @var int
     */
    private int $bin;

    /**
     * @var float
     */
    private float $amount;

    /**
     * @var string
     */
    private string $currency;

    /**
     * @var bool
     */
    private bool $eUIssued;

    public function __construct(int $bin, float $amount, string $currency, bool $eUIssued)
    {
        $this->bin = $bin;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->eUIssued = $eUIssued;
    }

    /**
     * @return int
     */
    public function getBin(): int
    {
        return $this->bin;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return bool
     */
    public function isEUIssued(): bool
    {
        return $this->eUIssued;
    }
}
