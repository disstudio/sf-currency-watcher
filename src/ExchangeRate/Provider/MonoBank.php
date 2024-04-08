<?php

namespace App\ExchangeRate\Provider;

use App\Currency\CurrencyCode;
use App\ExchangeRate\Rate;
use Exception;
use InvalidArgumentException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MonoBank implements ExchangeRateProviderInterface
{
    public function __construct(
        private string $name,
        private HttpClientInterface $httpClient,
        private string $endpointUrl,
    )
    {}

    public function getName(): string
    {
        return $this->name;
    }

    public function fetch(CurrencyCode $currencyCode, array $options = []): Rate
    {
        $baseCurrencyCodeValue = CurrencyCode::UAH->value;
        $currencyCodeValue = $currencyCode->value;

        $response = $this->httpClient->request('GET', $this->endpointUrl);
        $responseArray = json_decode($response->getContent(), true);
        if(false === $responseArray) {
            throw new Exception('Invalid JSON response');
        }

        foreach($responseArray as $responseItem) {
            if($responseItem['currencyCodeA'] !== $currencyCodeValue || $responseItem['currencyCodeB'] !== $baseCurrencyCodeValue) {
                continue;
            }
            
            return new Rate($this->name, $currencyCode, $responseItem['rateBuy'], $responseItem['rateSell']);
        }

        throw new Exception(sprintf('Currency rate for %s not found!', $currencyCodeValue));
    }
}
