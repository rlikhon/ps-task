<?php

declare(strict_types=1);

namespace CommissionGenerator\App\Rules;

use CommissionGenerator\App\Models\Transaction;
use CommissionGenerator\App\Models\TransactionBasket;

/**
 * Class WithdrawRule.
 */
abstract class WithdrawRule extends CommissionRule
{
    const OPERATION_TYPE = 'withdraw';

    public function canApply(TransactionBasket $basket, Transaction $transaction): bool
    {
        return $transaction->getOperationType() === self::OPERATION_TYPE;
    }
}
