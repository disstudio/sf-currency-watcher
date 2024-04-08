<?php

namespace App\ExchangeRate\Store;

use App\Currency\CurrencyCode;
use App\ExchangeRate\Rate;

class FileStore implements StoreInterface
{
    private array $data = [];

    public function __construct(
        private string $filePath,
    )
    {
        $this->readFile();
    }

    private function readFile(): void
    {
        if(!file_exists($this->filePath)) {
            return;
        }

        $rawData = file_get_contents($this->filePath);
        if(false === $rawData) {
            return;
        }

        $data = json_decode($rawData, true);
        if(false === $data) {
            return;
        }

        foreach($data as $key => $item) {
            $item['currencyCode'] = CurrencyCode::tryFrom($item['currencyCode']);
            if(is_null($item['currencyCode'])) {
                continue;
            }

            $this->data[$key] = $item;
        }
    }

    private function writeFile(): void
    {
        file_put_contents($this->filePath, json_encode($this->data, JSON_PRETTY_PRINT));
    }

    private function getKey(CurrencyCode $currencyCode, string $providerName): string
    {
        return sprintf('%s:%d', $providerName, $currencyCode->value);
    }

    public function get(CurrencyCode $currencyCode, string $providerName): ?Rate
    {
        $item = $this->data[$this->getKey($currencyCode, $providerName)] ?? null;
        if(!$item) {
            return null;
        }

        return new Rate($item['providerName'], $item['currencyCode'], $item['rateBuy'], $item['rateSell']);
    }

    public function set(Rate $rate): void
    {
        $this->data[$this->getKey($rate->getCurrencyCode(), $rate->getProviderName())] = [
            'providerName' => $rate->getProviderName(),
            'currencyCode' => $rate->getCurrencyCode()->value,
            'rateBuy' => $rate->getRateBuy(),
            'rateSell' => $rate->getRateSell(),
        ];

        $this->writeFile();
    }
}
