<?php

namespace App\Service;

use App\Currency\CurrencyCode;
use App\ExchangeRate\Diff as ExchangeRateDiff;
use App\ExchangeRate\Store\StoreInterface;
use App\Provider\ExchangeRateProviderInterface;
use Exception;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

class ExchangeRateWatcher
{
    /** @var ExchangeRateProviderInterface[] $providers */

    public function __construct(
        private NotifierInterface $notifier,
        private StoreInterface $store,
        private array $providers,
        private string $notificationEmail,
    )
    {}

    public function watch(CurrencyCode $currencyCode, float $rateThreshold): array
    {
        $messages = [];

        foreach($this->providers as $provider) {
            $diff = $this->getRateDiff($currencyCode, $provider->getName());

            // TODO: add translations/notification templates

            if($diff->getDiffBuy() > $rateThreshold) {
                $messages[] = sprintf('Provider %s: buy rate changed for %.2f (%s => %s)',
                    $provider->getName(), $diff->getDiffBuy(),
                    $this->formatRate($diff->getOldRate()?->getRateBuy()),
                    $this->formatRate($diff->getNewRate()?->getRateBuy())
                );
            }

            if($diff->getDiffSell() > $rateThreshold) {
                $messages[] = sprintf('Provider %s: sell rate changed for %.2f (%s => %s)',
                    $provider->getName(), $diff->getDiffSell(),
                    $this->formatRate($diff->getOldRate()?->getRateSell()),
                    $this->formatRate($diff->getNewRate()?->getRateSell())
                );
            }
        }

        if(count($messages)) {
            try {
                $this->sendNotification($messages);
            } catch(Exception $ex) {
                $messages[] = 'Error while sending notifications';
            }
        }

        return $messages;
    }

    private function formatRate(?float $rate): string
    {
        if(is_null($rate)) {
            return '(none)';
        }

        return number_format($rate, 2);
    }

    private function sendNotification(array $messages): void
    {
        $notification = (new Notification('Currency rate changed', ['email']))
            ->content(implode("\r\n", $messages));

        $recipient = new Recipient(
            $this->notificationEmail
        );

        $this->notifier->send($notification, $recipient);
    }

    private function getRateDiff(CurrencyCode $currencyCode, string $providerName, array $options = []): ExchangeRateDiff
    {
        if(!array_key_exists($providerName, $this->providers)) {
            throw new Exception(sprintf('Invalid provider: %s', $providerName));
        }

        /** @var ExchangeRateProviderInterface $provider */
        $provider = $this->providers[$providerName];

        $newRate = $provider->fetch($currencyCode, $options);
        $oldRate = $this->store->get($currencyCode, $providerName);

        $this->store->set($newRate);

        if(is_null($oldRate)) {
            $diff = new ExchangeRateDiff(.0, .0);
            $diff->setNewRate($newRate);
            return $diff;
        }

        return ExchangeRateDiff::fromRates($oldRate, $newRate);
    }
}
