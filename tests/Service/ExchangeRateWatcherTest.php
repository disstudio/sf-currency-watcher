<?php

namespace Tests\Service;

use App\Currency\CurrencyCode;
use App\Service\ExchangeRateWatcher;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ExchangeRateWatcherTest extends KernelTestCase
{
    public function testWatch(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $exchangeRateWatcher = $container->get(ExchangeRateWatcher::class);

        $this->assertIsArray($exchangeRateWatcher->watch(CurrencyCode::USD, 0));
    }
}
