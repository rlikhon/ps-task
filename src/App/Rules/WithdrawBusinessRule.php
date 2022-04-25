<?php

declare(strict_types=1);

namespace CommissionGenerator\App\Rules;

use CommissionGenerator\App\Models\Transaction;
use CommissionGenerator\App\Models\TransactionBasket;
use Money\Money;

/**
 * Class WithdrawBusinessRule.
 */
class WithdrawBusinessRule extends WithdrawRule
{
    const USER_TYPE = 'business';

    public function canApply(TransactionBasket $basket, Transaction $transaction): bool
    {
        return parent::canApply($basket, $transaction) && $transaction->getUserType() === self::USER_TYPE;
    }

    public function calculateFee(TransactionBasket $basket, Transaction $transaction): Money
    {
        return $transaction->getAmount()->multiply($this->getCommission());
    }
}
