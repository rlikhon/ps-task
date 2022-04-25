<?php

declare(strict_types=1);

namespace CommissionGenerator\App\Rules;

use CommissionGenerator\App\Models\Transaction;
use CommissionGenerator\App\Models\TransactionBasket;
use Money\Money;

/**
 * Class WithdrawPrivateAmountLessXPerWeekFreeRule.
 */
class WithdrawPrivateAmountLessXPerWeekFreeRule extends WithdrawPrivateRule
{
    /**
     * @var Money
     */
    private $amountPerWeek;

    /**
     * WithdrawPrivateAmountLessXPerWeekFreeRule constructor.
     */
    public function __construct(Money $amountPerWeek)
    {
        $this->amountPerWeek = $amountPerWeek;
        parent::__construct(0);
    }

    public function canApply(TransactionBasket $basket, Transaction $transaction): bool
    {
        return parent::canApply($basket, $transaction) && $basket->getAmountSum($transaction)->lessThanOrEqual(
                $this->amountPerWeek
            );
    }

    public function calculateFee(TransactionBasket $basket, Transaction $transaction): Money
    {
        return $transaction->getAmount()->multiply($this->getCommission());
    }
}
