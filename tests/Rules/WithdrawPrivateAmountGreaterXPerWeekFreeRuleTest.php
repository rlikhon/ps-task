<?php

declare(strict_types=1);

namespace CommissionGenerator\Tests\Rules;

use Carbon\Carbon;
use CommissionGenerator\App\Models\Transaction;
use CommissionGenerator\App\Models\TransactionBasket;
use CommissionGenerator\App\Rules\WithdrawPrivateAmountGreaterXPerWeekFreeRule;
use Money\Currency;
use Money\Money;


class WithdrawPrivateAmountGreaterXPerWeekFreeRuleTest extends RuleTest
{
    /**
     * @var WithdrawPrivateAmountGreaterXPerWeekFreeRule
     */
    private $depositRule;

    public function setUp()
    {
        $commission        = 0.003;
        $amountPerWeek     = 100000;
        $this->depositRule = new WithdrawPrivateAmountGreaterXPerWeekFreeRule(
            new Money($amountPerWeek, new Currency("EUR")), $commission
        );

        parent::setUp();
    }

    /**
     * @param array $transactionsArray
     * @param bool $expectation
     *
     * @dataProvider dataProviderForCanApply
     */
    public function testCanApply(array $transactionsArray, bool $expectation)
    {
        $transactionBasket = new TransactionBasket("EUR", $this->converter);
        foreach ($transactionsArray as $transactionItem) {

            $amount      = new Money($transactionItem[0], new Currency($transactionItem[2]));
            $transaction = new Transaction(
                new Carbon($transactionItem[1]), $transactionItem[3], $transactionItem[4], $transactionItem[5], $amount
            );
            $transactionBasket->add($transaction);
        }
        $this->assertEquals(
            $expectation,
            $this->depositRule->canApply($transactionBasket, $transaction)
        );
    }

    public function dataProviderForCanApply(): array
    {
        return [
            'private withdraw 10 EUR' => [[[1000, '2021-04-26', 'EUR', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW]], false],
            'private deposit 10 EUR' => [[[1000, '2021-04-26', 'EUR', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_DEPOSIT]], false],
            'business withdraw 10 EUR' => [[[1000, '2021-04-26', 'EUR', 1, RuleTest::USER_TYPE_BUSINESS, RuleTest::OPERATION_TYPE_WITHDRAW]], false],
            'business deposit 10 EUR' => [[[1000, '2021-04-26', 'EUR', 1, RuleTest::USER_TYPE_BUSINESS, RuleTest::OPERATION_TYPE_DEPOSIT]], false],
            'private withdraw 0 EUR' => [[[0, '2021-04-26', 'EUR', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW]], false],
            'private withdraw 1000 EUR' => [[[100000, '2021-04-26', 'EUR', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW]], false],
            'private withdraw 1000.01 EUR' => [[[1000001, '2021-04-26', 'EUR', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW]], true],
            'private withdraw 500+600 EUR' => [
                [
                    [50000, '2021-04-26', 'EUR', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW],
                    [60000, '2021-04-26', 'EUR', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW],
                ],
                true,
            ],
            'private withdraw 300+300+300+300 EUR' => [
                [
                    [30000, '2021-04-26', 'EUR', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW],
                    [30000, '2021-04-26', 'EUR', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW],
                    [30000, '2021-04-26', 'EUR', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW],
                    [30000, '2021-04-26', 'EUR', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW],
                ],
                true,
            ],
            'private withdraw 30+30+30 EUR' => [
                [
                    [3000, '2021-04-26', 'EUR', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW],
                    [3000, '2021-04-26', 'EUR', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW],
                    [3000, '2021-04-26', 'EUR', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW],
                ],
                false,
            ],
            'private withdraw 300+300+300+300 EUR different person' => [
                [
                    [30000, '2021-04-26', 'EUR', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW],
                    [30000, '2021-04-26', 'EUR', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW],
                    [30000, '2021-04-26', 'EUR', 2, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW],
                    [30000, '2021-04-26', 'EUR', 2, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW],
                ],
                false,
            ],
            'private withdraw 1000 EUR in JPY' => [[[129530, '2021-04-26', 'JPY', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW]], false],
            'private withdraw 1000.01 EUR in JPY' => [[[129531, '2021-04-26', 'JPY', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW]], true],
        ];
    }

    /**
     * @param array $transactionsArray
     * @param int $amountAfterCommission
     * @param string $amountAfterCommissionCurrency
     * @param bool $expectation
     *
     * @dataProvider dataProviderForFeeValue
     */
    public function testFeeValue(
        array $transactionsArray,
        int $amountAfterCommission,
        string $amountAfterCommissionCurrency,
        bool $expectation
    ) {
        $transactionBasket     = new TransactionBasket("EUR", $this->converter);
        $amountAfterCommission = new Money($amountAfterCommission, new Currency($amountAfterCommissionCurrency));
        foreach ($transactionsArray as $transactionItem) {
            $amount      = new Money($transactionItem[0], new Currency($transactionItem[2]));
            $transaction = new Transaction(
                new Carbon($transactionItem[1]), $transactionItem[3], $transactionItem[4], $transactionItem[5], $amount
            );
            $transactionBasket->add($transaction);
        }
        $this->assertEquals(
            $expectation,
            $this->depositRule->calculateFee($transactionBasket, $transaction)->equals($amountAfterCommission)
        );
    }

    public function dataProviderForFeeValue(): array
    {
        return [
            'private withdraw 1000.01 EUR' => [[[100001, '2021-04-26', 'EUR', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW]], 0, 'EUR', true],
            'private withdraw 500+600 EUR' => [
                [
                    [50000, '2021-04-26', 'EUR', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW],
                    [60000, '2021-04-26', 'EUR', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW],
                ],
                30,
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
                60,
                'EUR',
                true,
            ],
            'private withdraw 2000 EUR in JPY' => [[[259060, '2021-04-26', 'JPY', 1, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW]], 389, 'JPY',true],
        ];
    }
}
