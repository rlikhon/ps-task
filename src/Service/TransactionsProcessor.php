<?php

declare(strict_types=1);

namespace CommissionGenerator\Service;
 
use CommissionGenerator\App\Config\Configuration;
use CommissionGenerator\App\Factory\TransactionFactory;
use CommissionGenerator\App\Models\Transaction;
use CommissionGenerator\App\Models\TransactionBasket;
use CommissionGenerator\App\Rules\DepositRule;
use CommissionGenerator\App\Rules\TransactionRule;
use CommissionGenerator\App\Rules\WithdrawBusinessRule;
use CommissionGenerator\App\Rules\WithdrawPrivateAmountGreaterXPerWeekFreeRule;
use CommissionGenerator\App\Rules\WithdrawPrivateAmountLessXPerWeekFreeRule;
use CommissionGenerator\App\Rules\WithdrawPrivateMoreXTransactionsPerWeekRule;
use CommissionGenerator\Service\IOHelpers\InputByLine;
use CommissionGenerator\Service\IOHelpers\Output;
use Money\Currency;
use Money\Money;

/**
 * Class TransactionsProcessor.
 */
class TransactionsProcessor
{
    /**
     * @var InputByLine
     */
    protected $input;
    /**
     * @var Output
     */
    protected $output;
    /**
     * @var array
     */
    protected $rules;

    /**
     * TransactionsProcessor constructor.
     *
     * @param Output $outputcomposer
     */
    public function __construct(InputByLine $input, Output $output)
    {
        $this->input = $input;
        $this->output = $output;
        $freeFeeLimitCents = Configuration::getInstance()->get('FREE_FEE_LIMIT_CENTS');
        $baseCurrency = Configuration::getInstance()->get('BASE_CURRENCY');

        $this->rules = [
            new DepositRule((float) Configuration::getInstance()->get('RULE_DEPOSIT_FEE')),
            new WithdrawBusinessRule((float) Configuration::getInstance()->get('RULE_WITHDRAW_BUSINESS_FEE')),
            new WithdrawPrivateMoreXTransactionsPerWeekRule(
                (int) Configuration::getInstance()->get('FREE_FEE_NUMBER_PER_WEEK'),
                (float) Configuration::getInstance()->get('RULE_WITHDRAW_PRIVATE_FEE')
            ),
            new WithdrawPrivateAmountLessXPerWeekFreeRule(new Money($freeFeeLimitCents, new Currency($baseCurrency))),
            new WithdrawPrivateAmountGreaterXPerWeekFreeRule(
                new Money($freeFeeLimitCents, new Currency($baseCurrency)),
                (float) Configuration::getInstance()->get('RULE_WITHDRAW_PRIVATE_FEE')
            ),
        ];
    }

    private function getLastAplayebleRule(TransactionBasket $basket, Transaction $transaction): ?TransactionRule
    {
        $apply = null;
        foreach ($this->rules as $rule) {
            if ($rule->canApply($basket, $transaction)) {
                $apply = $rule;
            }
        }

        return $apply;
    }

    public function process()
    {
        $firstDayOfProcessedWeek = null;
        $transactionBasket = new TransactionBasket((string) Configuration::getInstance()->get('BASE_CURRENCY'), ExchangeRates::getRatesConverter());
        foreach ($this->input->getLine() as $line) {
            $transaction = TransactionFactory::createFromCSVLine($line);
            if (!(isset($firstDayOfProcessedWeek) && $transaction->getDate()->isSameWeek($firstDayOfProcessedWeek))) {
                $transactionBasket->clear();
                $firstDayOfProcessedWeek = $transaction->getDate();
            }

            $transactionBasket->add($transaction);
            $transactionRule = $this->getLastAplayebleRule($transactionBasket, $transaction);
            $commission = $transactionRule->calculateFee($transactionBasket, $transaction);
            $this->output->output($commission);
        }
    }
}
