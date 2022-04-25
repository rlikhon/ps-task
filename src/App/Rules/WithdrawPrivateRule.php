<?php

declare(strict_types=1);

namespace CommissionGenerator\App\Rules;

use CommissionGenerator\App\Models\Transaction;
use CommissionGenerator\App\Models\TransactionBasket;

/**
 * Class WithdrawPrivateRule.
 */
abstract class WithdrawPrivateRule extends WithdrawRule
{
    const USER_TYPE = 'private';

    public function canApply(TransactionBasket $basket, Transaction $transaction): bool
    {
        return parent::canApply($basket, $transaction) && $transaction->getUserType() === self::USER_TYPE;
    }
}
