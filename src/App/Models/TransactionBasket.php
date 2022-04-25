<?php

declare(strict_types=1);

namespace CommissionGenerator\App\Models;

use Money\Converter;
use Money\Currency;
use Money\Money;

/**
 * Class TransactionBasket.
 */
class TransactionBasket
{
    /**
     * list of transactions grouped by user id and operation type.
     *
     * @var array
     */
    protected $transactionsByUserAndType = [];

    /**
     * @var string
     */
    protected $baseCurrency;

    /** @var Converter */
    protected $converter;

    /**
     * TransactionBasket constructor.
     */
    public function __construct(string $baseCurrency, Converter $converter)
    {
        $this->baseCurrency = $baseCurrency;
        $this->converter = $converter;
    }

    /**
     * Clear list of transactions.
     */
    public function clear(): void
    {
        $this->transactionsByUserAndType = [];
    }

    /**
     * Add transaction to list.
     */
    public function add(Transaction $transaction): void
    {
        $this->transactionsByUserAndType[$transaction->getUserId()][$transaction->getOperationType()][] = $transaction;
    }

    /**
     * Sum all transactions by user id and type from $transaction.
     */
    public function getAmountSum(Transaction $transaction): Money
    {
        $sum = new Money(0, new Currency($this->baseCurrency));

        if (!isset(
                $this->transactionsByUserAndType[$transaction->getUserId()]
            ) || !isset(
                $this->transactionsByUserAndType[$transaction->getUserId()][$transaction->getOperationType()]
            )) {
            return $sum;
        }

        foreach ($this->transactionsByUserAndType[$transaction->getUserId()][$transaction->getOperationType(
        )] as $savedTransaction) {
            /** @var Transaction $savedTransaction */
            $converted = $this->converter->convert(
                $savedTransaction->getAmount(),
                $sum->getCurrency()
            );
            $sum = $sum->add($converted);
        }

        return $sum;
    }

    /**
     * Count all transactions by user id and type from $transaction.
     */
    public function getCount(Transaction $transaction): float
    {
        if (!isset(
                $this->transactionsByUserAndType[$transaction->getUserId()]
            ) || !isset(
                $this->transactionsByUserAndType[$transaction->getUserId()][$transaction->getOperationType()]
            )) {
            return 0;
        }

        return count($this->transactionsByUserAndType[$transaction->getUserId()][$transaction->getOperationType()]);
    }

    public function getConverter(): Converter
    {
        return $this->converter;
    }
}
