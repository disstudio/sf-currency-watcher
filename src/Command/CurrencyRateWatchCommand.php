<?php

namespace App\Command;

use App\Currency\CurrencyCode;
use App\Service\ExchangeRateWatcher;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:currency-rate:watch',
    description: 'Watch currency rate for changing.',
    hidden: false
)]
class CurrencyRateWatchCommand extends Command
{
    public function __construct(private ExchangeRateWatcher $exchangeRateWatcher)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('currency_code', InputArgument::REQUIRED, 'Currency name (USD/EUR etc.)')
            ->addOption('rate_threshold', 't', InputOption::VALUE_REQUIRED, 'If rate changed above this thresholdm notification will be sent', .0)
            ->setHelp(
                'This command pulls currency exchange rate from different providers ' .
                'and checks if it changed above specified threshold. ' .
                'If latter, SMS or email notifications are sent.'
                )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $currencyCode = null;
            foreach(CurrencyCode::cases() as $currencyCodeCase) {
                if($currencyCodeCase->name === $input->getArgument('currency_code')) {
                    $currencyCode = $currencyCodeCase;
                    break;
                }
            }
            if(is_null($currencyCode)) {
                throw new Exception(sprintf('Invalid currency code: %s', $input->getArgument('currency_code')));
            }
            
            $messages = $this->exchangeRateWatcher->watch($currencyCode, (float) $input->getOption('rate_threshold'));
            if(count($messages)) {
                $output->write($messages, true);
            } else {
                $output->writeln('Rates not changed');
            }
        } catch(Exception $ex) {
            $io->error($ex->getMessage());
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }
}
