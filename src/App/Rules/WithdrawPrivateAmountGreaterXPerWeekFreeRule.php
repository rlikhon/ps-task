<?php

declare(strict_types=1);

namespace CommissionGenerator\App\Rules;

use CommissionGenerator\App\Models\Transaction;
use CommissionGenerator\App\Models\TransactionBasket;
use Money\Money;

/**
 * Class WithdrawPrivateAmountGreaterXPerWeekFreeRule.
 */
class WithdrawPrivateAmountGreaterXPerWeekFreeRule extends WithdrawPrivateRule
{
    /**
     * @var Money
     */
    private $amountPerWeek;

    /**
     * WithdrawPrivateAmountGreaterXPerWeekFreeRule constructor.
     */
    public function __construct(Money $amountPerWeek, float $commission)
    {
        $this->amountPerWeek = $amountPerWeek;
        parent::__construct($commission);
    }

    public function canApply(TransactionBasket $basket, Transaction $transaction): bool
    {
        return parent::canApply($basket, $transaction) && $basket->getAmountSum($transaction)->greaterThan(
                $this->amountPerWeek
            );
    }

    public function calculateFee(TransactionBasket $basket, Transaction $transaction): Money
    {
        $delta = $basket->getAmountSum($transaction)->subtract($this->amountPerWeek);
        $delta = $basket->getConverter()->convert($delta, $transaction->getAmount()->getCurrency());
        if ($delta->greaterThan($transaction->getAmount())) {
            $delta = $transaction->getAmount();
        }

        return $delta->multiply($this->getCommission());
    }
}
