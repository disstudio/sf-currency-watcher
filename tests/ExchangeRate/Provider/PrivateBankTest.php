<?php

namespace Tests\ExchangeRate\Provider;

use App\Currency\CurrencyCode;
use App\ExchangeRate\Provider\PrivatBank;
use App\ExchangeRate\Rate;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PrivateBankTest extends TestCase
{
    public function testInit(): void
    {
        $name = 'privatbank';
        $privatBank = new PrivatBank($name, $this->createMock(HttpClientInterface::class), 'http://localhost', 0);

        $this->assertEquals($name, $privatBank->getName());
    }
}