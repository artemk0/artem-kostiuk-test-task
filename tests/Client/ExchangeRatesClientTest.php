<?php

declare(strict_types=1);

use App\Client\ExchangeRatesClient;
use App\Client\SimpleJsonHttpClient;
use App\Exceptions\CanNotGetResponseFrom3rdParty;
use PHPUnit\Framework\TestCase;

class ExchangeRatesClientTest extends TestCase
{
    /**
     * @dataProvider getRatesProvider
     */
    public function testGetRates(int $binNumber, string $response, $expected, $optional = '')
    {
        $simpleJsonHttpClientMock = $this->getMockBuilder(SimpleJsonHttpClient::class)
            ->onlyMethods(['getResponse'])
            ->getMock()
        ;

        $simpleJsonHttpClientMock
            ->method('getResponse')
            ->willReturn($response)
        ;

        $exchangeRatesClient = new ExchangeRatesClient($simpleJsonHttpClientMock);

        if (in_array($binNumber, [42, 4242])) {
            $this->expectException($expected);
            $this->expectExceptionMessage($optional);
            $exchangeRatesClient->getRates();
        }

        if ($binNumber === 424242) {
            $this->assertCount($expected, $exchangeRatesClient->getRates());
        }
    }

    /**
     * @dataProvider getRateProvider
     */
    public function testGetRate(int $binNumber, string $response, $expected, $optional = '')
    {
        $simpleJsonHttpClientMock = $this->getMockBuilder(SimpleJsonHttpClient::class)
            ->onlyMethods(['getResponse'])
            ->getMock()
        ;

        $simpleJsonHttpClientMock
            ->method('getResponse')
            ->willReturn($response)
        ;

        $exchangeRatesClient = new ExchangeRatesClient($simpleJsonHttpClientMock);

        if ($binNumber === 42) {
            $this->expectException($expected);
            $this->expectExceptionMessage($optional);
            $exchangeRatesClient->getRate('UNKNOWN_CURRENCY');
        }

        $this->assertEquals($expected, $exchangeRatesClient->getRate('EUR'));
    }

    public function getRatesProvider(): array
    {
        return [
            'Exception empty response' => [42, '', CanNotGetResponseFrom3rdParty::class, 'Empty response'],
            'Exception failed to decode response' => [4242, '{42', CanNotGetResponseFrom3rdParty::class, 'Can not parse JSON. Error: Syntax error'],
            'Success response' => [424242, '{"base": "USD","date": "2021-03-17","rates": {"EUR": 0.813399,"GBP": 0.72007,"JPY": 107.346001},"success": true,"timestamp": 1519296206}', 3],
        ];
    }

    public function getRateProvider(): array
    {
        return [
            'Exception unknown currency' => [42, '{"base": "USD","date": "2021-03-17","rates": {"EUR": 0.813399,"GBP": 0.72007,"JPY": 107.346001},"success": true,"timestamp": 1519296206}', CanNotGetResponseFrom3rdParty::class, 'Currency "UNKNOWN_CURRENCY" does not have a rate.'],
            'Success response' => [4242, '{"base": "USD","date": "2021-03-17","rates": {"EUR": 0.813399,"GBP": 0.72007,"JPY": 107.346001},"success": true,"timestamp": 1519296206}', 0.813399],
        ];
    }
}
