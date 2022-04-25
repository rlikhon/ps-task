<?php

declare(strict_types=1);

namespace CommissionGenerator\App\Rules;

use CommissionGenerator\App\Models\Transaction;
use CommissionGenerator\App\Models\TransactionBasket;
use Money\Money;

/**
 * Class DepositRule.
 */
class DepositRule extends CommissionRule
{
    const OPERATION_TYPE = 'deposit';

    public function canApply(TransactionBasket $basket, Transaction $transaction): bool
    {
        return $transaction->getOperationType() === self::OPERATION_TYPE;
    }

    public function calculateFee(TransactionBasket $basket, Transaction $transaction): Money
    {
        return $transaction->getAmount()->multiply($this->getCommission());
    }
}
