<?php

declare(strict_types=1);

namespace CommissionGenerator\App\Rules;

use CommissionGenerator\App\Models\Transaction;
use CommissionGenerator\App\Models\TransactionBasket;
use Money\Money;

/**
 * Class WithdrawPrivateMoreXTransactionsPerWeekRule.
 */
class WithdrawPrivateMoreXTransactionsPerWeekRule extends WithdrawPrivateRule
{
    /** @var int */
    private $numberPerWeek = 0;

    /**
     * WithdrawPrivateMoreXTransactionsPerWeekRule constructor.
     */
    public function __construct(int $numberPerWeek, float $commission)
    {
        $this->numberPerWeek = $numberPerWeek;
        parent::__construct($commission);
    }

    public function canApply(TransactionBasket $basket, Transaction $transaction): bool
    {
        return parent::canApply($basket, $transaction) && $basket->getCount($transaction) > $this->numberPerWeek;
    }

    public function calculateFee(TransactionBasket $basket, Transaction $transaction): Money
    {
        return $transaction->getAmount()->multiply($this->getCommission());
    }
}
