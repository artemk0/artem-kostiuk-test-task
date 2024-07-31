<?php

namespace tests\Client;

use App\Client\BinClient;
use App\Client\SimpleJsonHttpClient;
use App\Exceptions\CanNotGetResponseFrom3rdParty;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class BinClientTest extends TestCase
{
    #[DataProvider('getAlpha2CountryCodeResponseProvider')]
    public function testGetAlpha2CountryCode(int $binNumber, string $response, string $expected, $optional = ''): void
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

    #[DataProvider('isEUIssuedProvider')]
    public function testIsEUIssued(int $binNumber, string $response, bool $expected): void
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

    public static function getAlpha2CountryCodeResponseProvider(): iterable
    {
        yield 'Exception empty response' => [42, '', CanNotGetResponseFrom3rdParty::class, 'Empty response'];
        yield 'Exception failed to decode response' => [4242, '{42', CanNotGetResponseFrom3rdParty::class, 'Can not parse JSON. Error: Syntax error'];
        yield 'Success response' => [424242, '{"country":{"alpha2":"UA"}}', 'UA'];
    }

    public static function isEUIssuedProvider(): iterable
    {
        yield 'Issued in EU' => [42, '{"country":{"alpha2":"SE"}}', true];
        yield 'Not issued in EU' => [4242, '{"country":{"alpha2":"UA"}}', false];
    }
}
