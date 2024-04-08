<?php

namespace App\ExchangeRate;

use App\Currency\CurrencyCode;

class Rate
{
    public function __construct(
        private string $providerName,
        private CurrencyCode $currencyCode,
        private float $rateBuy,
        private float $rateSell,
    )
    {}

    public function getProviderName(): string
    {
        return $this->providerName;
    }

    public function getCurrencyCode(): CurrencyCode
    {
        return $this->currencyCode;
    }

    public function getRateBuy(): float
    {
        return $this->rateBuy;
    }

    public function getRateSell(): float
    {
        return $this->rateSell;
    }
}
