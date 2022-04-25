<?php

declare(strict_types=1);

namespace CommissionGenerator\App\Rules;

use CommissionGenerator\App\Models\Transaction;
use CommissionGenerator\App\Models\TransactionBasket;
use Money\Money;

/**
 * Interface TransactionRule.
 */
interface TransactionRule
{
    public function canApply(TransactionBasket $basket, Transaction $transaction): bool;

    public function calculateFee(TransactionBasket $basket, Transaction $transaction): Money;
}
