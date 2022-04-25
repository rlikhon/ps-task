<?php

declare(strict_types=1);

namespace CommissionGenerator\Tests\Rules;

use Carbon\Carbon;
use CommissionGenerator\App\Models\Transaction;
use CommissionGenerator\App\Models\TransactionBasket;
use CommissionGenerator\App\Rules\DepositRule;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class DepositRuleTest extends RuleTest
{
    /**
     * @var DepositRule
     */
    private $depositRule;

    public function setUp()
    {
        $commission        = 0.005;
        $this->depositRule = new DepositRule($commission);
        parent::setUp();
    }

    /**
     * @param int $amount
     * @param string $userType
     * @param string $operationType
     * @param bool $expectation
     *
     * @dataProvider dataProviderForCanApply
     */
    public function testCanApply(int $amount, string $userType, string $operationType, bool $expectation)
    {
        $amount            = new Money($amount, new Currency("EUR"));
        $transaction       = new Transaction(new Carbon("2021-04-26"), 1, $userType, $operationType, $amount);
        $transactionBasket = new TransactionBasket("EUR", $this->converter);
        $transactionBasket->add($transaction);
        $this->assertEquals(
            $expectation,
            $this->depositRule->canApply($transactionBasket, $transaction)
        );
    }

    public function dataProviderForCanApply(): array
    {
        return [
            'private withdraw 10 EUR' => [1000, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_WITHDRAW, false],
            'private deposit 10 EUR' => [1000, RuleTest::USER_TYPE_PRIVATE, RuleTest::OPERATION_TYPE_DEPOSIT, true],
            'business withdraw 10 EUR' => [1000, RuleTest::USER_TYPE_BUSINESS, RuleTest::OPERATION_TYPE_WITHDRAW, false],
            'business deposit 10 EUR' => [1000, RuleTest::USER_TYPE_BUSINESS, RuleTest::OPERATION_TYPE_DEPOSIT, true],
            'business deposit 0 EUR' => [0, RuleTest::USER_TYPE_BUSINESS, RuleTest::OPERATION_TYPE_DEPOSIT, true],
            'business deposit 1000 EUR' => [100000, RuleTest::USER_TYPE_BUSINESS, RuleTest::OPERATION_TYPE_DEPOSIT, true],
        ];
    }

    /**
     * @param int $amount
     * @param string $userType
     * @param string $operationType
     * @param int $amountAfterCommission
     * @param bool $expectation
     *
     * @dataProvider dataProviderForFeeValue
     */
    public function testFeeValue(
        int $amount,
        string $userType,
        string $operationType,
        int $amountAfterCommission,
        bool $expectation
    ) {
        $amount                = new Money($amount, new Currency("EUR"));
        $amountAfterCommission = new Money($amountAfterCommission, new Currency("EUR"));
        $transaction           = new Transaction(new Carbon("2021-04-26"), 1, $userType, $operationType, $amount);
        $transactionBasket     = new TransactionBasket("EUR", $this->converter);
        $transactionBasket->add($transaction);
        $this->assertEquals(
            $expectation,
            $this->depositRule->calculateFee($transactionBasket, $transaction)->equals($amountAfterCommission)
        );
    }

    public function dataProviderForFeeValue(): array
    {
        return [
            'business deposit 0 EUR' => [0, RuleTest::USER_TYPE_BUSINESS, RuleTest::OPERATION_TYPE_DEPOSIT, 0, true],
            'business deposit 10 EUR' => [1000, RuleTest::USER_TYPE_BUSINESS, RuleTest::OPERATION_TYPE_DEPOSIT, 5, true],
            'business deposit 1000 EUR' => [100000, RuleTest::USER_TYPE_BUSINESS, RuleTest::OPERATION_TYPE_DEPOSIT, 500, true],
        ];
    }

}
