<?php

namespace App\ExchangeRate\Store;

use App\Currency\CurrencyCode;
use App\ExchangeRate\Rate;

interface StoreInterface
{
    public function get(CurrencyCode $currencyCode, string $providerName): ?Rate;

    public function set(Rate $rate): void;
}
