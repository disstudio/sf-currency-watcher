<?php

namespace App\ExchangeRate\Provider;

use App\Currency\CurrencyCode;
use App\ExchangeRate\Rate;
use Exception;
use InvalidArgumentException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PrivatBank implements ExchangeRateProviderInterface
{
    private const CURRENCY_MAP = [
        CurrencyCode::EUR->value => 'EUR',
        CurrencyCode::USD->value => 'USD',
    ];

    public function __construct(
        private string $name,
        private HttpClientInterface $httpClient,
        private string $endpointUrl,
        private int $defaultCoursId,
    )
    {}

    public function getName(): string
    {
        return $this->name;
    }

    public function fetch(CurrencyCode $currencyCode, array $options = []): Rate
    {
        if(!array_key_exists($currencyCode->value, self::CURRENCY_MAP)) {
            throw new InvalidArgumentException('Invalid currency code');
        }
        $currencyName = self::CURRENCY_MAP[$currencyCode->value];

        $url = $this->endpointUrl . '?' . http_build_query(['json' => '', 'coursid' => $options['coursid'] ?? $this->defaultCoursId]);

        $response = $this->httpClient->request('GET', $url);
        $responseArray = json_decode($response->getContent(), true);
        if(false === $responseArray) {
            throw new Exception('Invalid JSON response');
        }

        foreach($responseArray as $responseItem) {
            if($responseItem['ccy'] !== $currencyName) {
                continue;
            }

            return new Rate($this->name, $currencyCode, $responseItem['buy'], $responseItem['sale']);
        }

        throw new Exception(sprintf('Currency rate for %s not found!', $currencyName));
    }
}
