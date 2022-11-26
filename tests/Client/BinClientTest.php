<?php

declare(strict_types=1);

use App\Client\BinClient;
use App\Client\SimpleJsonHttpClient;
use App\Exceptions\CanNotGetResponseFrom3rdParty;
use PHPUnit\Framework\TestCase;

class BinClientTest extends TestCase
{
    /**
     * @dataProvider getAlpha2CountryCodeResponseProvider
     */
    public function testGetAlpha2CountryCode(int $binNumber, string $response, $expected, $optional = '')
    {
        $simpleJsonHttpClientMock = $this->getMockBuilder(SimpleJsonHttpClient::class)
            ->onlyMethods(['getResponse'])
            ->getMock()
        ;

        $simpleJsonHttpClientMock
            ->method('getResponse')
            ->willReturn($response)
        ;

        $binClient = new BinClient($simpleJsonHttpClientMock);

        if (in_array($binNumber, [42, 4242])) {
            $this->expectException($expected);
            $this->expectExceptionMessage($optional);
        }
        $this->assertEquals($expected, $binClient->getAlpha2CountryCode($binNumber));
    }

    /**
     * @dataProvider isEUIssuedProvider
     */
    public function testIsEUIssued(int $binNumber, string $response, $expected)
    {
        $simpleJsonHttpClientMock = $this->getMockBuilder(SimpleJsonHttpClient::class)
            ->onlyMethods(['getResponse'])
            ->getMock()
        ;

        $simpleJsonHttpClientMock
            ->method('getResponse')
            ->willReturn($response)
        ;

        $binClient = new BinClient($simpleJsonHttpClientMock);

        $this->assertEquals($expected, $binClient->isEUIssued($binNumber));
    }

    public function getAlpha2CountryCodeResponseProvider(): array
    {
        return [
            'Exception empty response' => [42, '', CanNotGetResponseFrom3rdParty::class, 'Empty response'],
            'Exception failed to decode response' => [4242, '{42', CanNotGetResponseFrom3rdParty::class, 'Can not parse JSON. Error: Syntax error'],
            'Success response' => [424242, '{"country":{"alpha2":"UA"}}', 'UA'],
        ];
    }

    public function isEUIssuedProvider(): array
    {
        return [
            'Issued in EU' => [42, '{"country":{"alpha2":"SE"}}', true],
            'Not issued in EU' => [4242, '{"country":{"alpha2":"UA"}}', false],
        ];
    }
}
