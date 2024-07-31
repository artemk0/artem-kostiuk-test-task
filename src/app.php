<?php

use App\Client;
use App\Exceptions\CanNotGetResponseFrom3rdParty;
use App\Exceptions\FailedToLoadTransactions;
use App\Service;

require_once __DIR__ . '/../vendor/autoload.php';

$httpClient = new Client\SimpleJsonHttpClient();

$exchangeRatesClient = new Client\ExchangeRatesClient($httpClient);

$client = $argv[2] ?? 'online';
if ($client === 'online') {
    $binClient = new Client\BinClient($httpClient);
} else {
    $binClient = new Client\BinLocalClient();
}
$fileReader = new Service\FileReader($argv[1]);

try {
    $transactionLoader = (new Service\TransactionLoader($binClient, $fileReader))->loadFromFile();

    $commissions = (new Service\CommissionCalculator($transactionLoader->getTransactions(), $exchangeRatesClient))
        ->calcCommissions()
        ->getCommissions()
    ;

    echo implode("\n", array_column($commissions, 'value')) . "\n";

    if ($transactionLoader->hasFailedTransactions()) {
        echo "\n\nFailed transactions:\n";
        foreach ($transactionLoader->getFailedTransactions() as $failedTransaction) {
            echo "Line:\t" . $failedTransaction['line'] . "\t" . $failedTransaction['exception']->getMessage() . "\n";
        }
    }
} catch (CanNotGetResponseFrom3rdParty $e) {
    die('Failed to get response from 3rd party API. Message: ' . $e->getMessage());
} catch (FailedToLoadTransactions $e) {
    die('Failed to load transactions. Message: ' . $e->getMessage());
} catch (Exception $e) {
    die('Unexpected error. Message: ' . $e->getMessage());
}

