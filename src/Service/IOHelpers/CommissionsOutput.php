<?php

declare(strict_types=1);

namespace CommissionGenerator\Service\IOHelpers;

use Money\Currencies\ISOCurrencies;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;

/**
 * Class CommissionsOutput.
 */
class CommissionsOutput implements Output
{
    public function output(Money $data)
    {
        $currencies = new ISOCurrencies();

        $moneyFormatter = new DecimalMoneyFormatter($currencies);

        echo $moneyFormatter->format($data)."\n";
    }
}
