<?php

declare(strict_types=1);

namespace App\Service;

use App\Client\BinClient;
use App\Entity\Transaction;
use App\Exceptions\FailedToLoadSingleTransaction;
use App\Exceptions\FailedToLoadTransactions;

class TransactionLoader
{
    /**
     * @var BinClient
     */
    private BinClient $binClient;

    /**
     * @var FileReader
     */
    private FileReader $fileReader;

    /**
     * @var array
     */
    private array $transactions;

    /**
     * @var array
     */
    private array $failedTransactions;

    public function __construct(BinClient $binClient, FileReader $fileReader)
    {
        $this->binClient = $binClient;
        $this->fileReader = $fileReader;
    }

    public function loadFromFile(): self
    {
        $lines = $this->fileReader->readFile();

        if (empty($lines)) {
            throw new FailedToLoadTransactions('Can not read file "' . $this->fileReader->getFilename() . '".');
        }

        foreach ($lines as $line => $row) {
            try {
                $this->transactions[] = $this->parseRow($row);
            } catch (FailedToLoadSingleTransaction $e) {
                $this->failedTransactions[] = [
                    'line' => $line,
                    'exception' => $e,
                ];

                continue;
            }
        }

        return $this;
    }

    private function parseRow(string $row): Transaction
    {
        $result = json_decode($row, true);
        if ($result === null) {
            throw new FailedToLoadSingleTransaction('Can not parse JSON. Error: ' . json_last_error_msg());
        }

        foreach (['bin', 'amount', 'currency'] as $key) {
            if (empty($result[$key])) {
                throw new FailedToLoadSingleTransaction('Not found required key: ' . $key);
            }
        }

        $bin = (int)$result['bin'];
        $amount = (float)$result['amount'];
        $currency = (string)$result['currency'];

        $eUIssued = $this->binClient->isEUIssued($bin);

        return new Transaction($bin, $amount, $currency, $eUIssued);
    }

    /**
     * @return array
     */
    public function getTransactions(): array
    {
        return $this->transactions;
    }

    /**
     * @return bool
     */
    public function hasFailedTransactions(): bool
    {
        return !empty($this->failedTransactions);
    }

    /**
     * @return array
     */
    public function getFailedTransactions(): array
    {
        return $this->failedTransactions;
    }
}
