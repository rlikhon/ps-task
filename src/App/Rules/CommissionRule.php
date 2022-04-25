<?php

declare(strict_types=1);

namespace CommissionGenerator\App\Rules;

/**
 * Class CommissionRule.
 */
abstract class CommissionRule implements TransactionRule
{
    /** @var float */
    private $commission;

    /**
     * CommissionRule constructor.
     */
    public function __construct(float $commission)
    {
        $this->commission = $commission;
    }

    public function getCommission(): float
    {
        return $this->commission;
    }
}
