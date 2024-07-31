<?php

namespace App\Service;

use App\Client\BinClient;
use App\Client\BinClientInterface;
use App\Entity\Transaction;
use App\Exceptions\FailedToLoadSingleTransaction;
use App\Exceptions\FailedToLoadTransactions;

class TransactionLoader
{
    public function __construct(
        private BinClientInterface $binClient,
        private FileReader $fileReader,
        private array $transactions = [],
        private array $failedTransactions = []
    ) {}

    public function loadFromFile(): self
    {
        $lines = $this->fileReader->readFile();

        if (count($lines) === 0 || $lines === false) {
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
            if (!isset($result[$key])) {
                throw new FailedToLoadSingleTransaction('Not found required key: ' . $key);
            }
        }

        $bin = (int)$result['bin'];
        $amount = (float)$result['amount'];
        $currency = (string)$result['currency'];

        $eUIssued = $this->binClient->isEUIssued($bin);

        return new Transaction($bin, $amount, $currency, $eUIssued);
    }

    public function getTransactions(): array
    {
        return $this->transactions;
    }

    public function hasFailedTransactions(): bool
    {
        return !empty($this->failedTransactions);
    }

    public function getFailedTransactions(): array
    {
        return $this->failedTransactions;
    }
}
