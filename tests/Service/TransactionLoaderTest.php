<?php

namespace tests\Service;

use App\Client\BinClient;
use App\Exceptions\FailedToLoadTransactions;
use App\Service\FileReader;
use App\Service\TransactionLoader;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TransactionLoaderTest extends TestCase
{
    #[DataProvider('loadFromFileProvider')]
    public function testLoadFromFile(string $filename, bool $isEUIssued, array $lines, int|string $expected, int|string $optional = ''): void
    {
        $binClientMock = $this->getMockBuilder(BinClient::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isEUIssued'])
            ->getMock()
        ;

        $binClientMock
            ->method('isEUIssued')
            ->willReturn($isEUIssued)
        ;

        $fileReaderMock = $this->getMockBuilder(FileReader::class)
            ->setConstructorArgs([$filename])
            ->onlyMethods(['readFile'])
            ->getMock()
        ;

        $fileReaderMock
            ->method('readFile')
            ->willReturn($lines)
        ;

        if ($filename === '42') {
            $this->expectException($expected);
            $this->expectExceptionMessage($optional);
        }

        $transactionLoader = (new TransactionLoader($binClientMock, $fileReaderMock))->loadFromFile();
        if (in_array($filename, ['4242', '424242'])) {
            $this->assertCount($expected, $transactionLoader->getTransactions());
            $this->assertCount($optional, $transactionLoader->getFailedTransactions());
        }
    }

    public static function loadFromFileProvider(): iterable
    {
        yield 'Can not read file' => ['42', false, [], FailedToLoadTransactions::class, 'Can not read file "42"'];
        yield 'JSON for single transaction is malformed' => ['4242', false, [
                '{"bin":"1","amount":"100.00","currency":"EUR"}',
                '{"bin""2","amount":"50.00","currency":"USD"}',
                '{"bin":"45417360","amount":"10000.00","currency":"JPY"}',
            ], 2, 1];
        yield 'Missing two keys in JSON' => ['424242', false, [
                '{"bi":"1","amount":"100.00","currency":"EUR"}',
                '{"bin":"2","ammount":"50.00","currency":"USD"}',
                '{"bin":"45417360","amount":"10000.00","currency":"JPY"}',
            ], 1, 2];
    }
}
