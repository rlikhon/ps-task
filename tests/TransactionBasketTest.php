<?php
declare(strict_types=1);

namespace CommissionGenerator\Tests;

use Carbon\Carbon;
use CommissionGenerator\App\Models\Transaction;
use CommissionGenerator\App\Models\TransactionBasket;
use CommissionGenerator\App\Rules\DepositRule;
use CommissionGenerator\Tests\Rules\RuleTest;
use Money\Currency;
use Money\Money;

class TransactionBasketTest extends RuleTest
{
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @param array $transactionsArray
     * @param int $amountSum
     * @param string $amountSumCurrency
     * @param bool $expectation
     *
     * @dataProvider dataProviderForTestSum
     */
    public function testSum(
        array $transactionsArray,
        int $amountSum,
        string $amountSumCurrency,
        bool $expectation
    ) {
        $transactionBasket = new TransactionBasket("EUR", $this->converter);
        $amountSum         = new Money($amountSum, new Currency($amountSumCurrency));
        foreach ($transactionsArray as $transactionItem) {
            $amount      = new Money($transactionItem[0], new Currency($transactionItem[2]));
            $transaction = new Transaction(
                new Carbon($transactionItem[1]), $transactionItem[3], $transactionItem[4], $transactionItem[5], $amount
            );
            $transactionBasket->add($transaction);
        }
        $this->assertEquals(
            $expectation,
            $transactionBasket->getAmountSum($transaction)->equals($amountSum)
        );
    }

    public function dataProviderForTestSum(): array
    {
        return [
            'private withdraw 1000.01 EUR' => [[[100001, '2021-04-26', 'EUR', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW]], 100001, 'EUR', true],
            'private withdraw 500+600 EUR' => [
                [
                    [50000, '2021-04-26', 'EUR', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW],
                    [60000, '2021-04-26', 'EUR', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW],
                ],
                110000,
                'EUR',
                true,
            ],
            'private withdraw 300+300+300+300 EUR' => [
                [
                    [30000, '2021-04-26', 'EUR', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW],
                    [30000, '2021-04-26', 'EUR', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW],
                    [30000, '2021-04-26', 'EUR', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW],
                    [30000, '2021-04-26', 'EUR', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW],
                ],
                120000,
                'EUR',
                true,
            ],
            'private withdraw 2000 EUR in JPY' => [[[259060, '2021-04-26', 'JPY', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW]], 200000, 'EUR',true],
        ];
    }
}