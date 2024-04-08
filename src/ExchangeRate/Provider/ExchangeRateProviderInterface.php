<?php

namespace App\ExchangeRate\Provider;

use App\Currency\CurrencyCode;
use App\ExchangeRate\Rate;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.exchange_rate_provider')]
interface ExchangeRateProviderInterface
{
    public function getName(): string;

    /**
     * @param CurrencyCode $currencyCode Currency code in ISO 4217
     * @param array $options Provider-specific options (optional)
     */
    public function fetch(CurrencyCode $currencyCode, array $options = []): Rate;
}
