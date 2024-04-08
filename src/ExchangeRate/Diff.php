<?php

namespace App\ExchangeRate;

class Diff
{
    public function __construct(
        private float $diffBuy,
        private float $diffSell,
        private ?Rate $oldRate = null,
        private ?Rate $newRate = null,
    )
    {}

    public static function fromRates(Rate $oldRate, Rate $newRate): self
    {
        return new self(
            $newRate->getRateBuy() - $oldRate->getRateBuy(),
            $newRate->getRateSell() - $oldRate->getRateSell(),
            $oldRate, $newRate
        );
    }

    public function getDiffBuy(): float
    {
        return $this->diffBuy;
    }

    public function getDiffSell(): float
    {
        return $this->diffSell;
    }

    public function getOldRate(): ?Rate
    {
        return $this->oldRate;
    }

    public function getNewRate(): ?Rate
    {
        return $this->newRate;
    }

    public function setNewRate(?Rate $newRate): void
    {
        $this->newRate = $newRate;
    }
}
