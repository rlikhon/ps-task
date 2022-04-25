<?php

declare(strict_types=1);

namespace CommissionGenerator\App\Factory;

use Carbon\Carbon;
use CommissionGenerator\App\Models\Transaction;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Parser\DecimalMoneyParser;

/**
 * Class TransactionFactory.
 */
class TransactionFactory
{
    /**
     * Convert csv line to Transaction model.
     */
    public static function createFromCSVLine(array $line): Transaction
    {
        $currencies = new ISOCurrencies();

        $moneyParser = new DecimalMoneyParser($currencies);

        $date = Carbon::createFromFormat('Y-m-d', $line[0]);
        $userId = (int) $line[1];
        $userType = $line[2];
        $operationType = $line[3];
        $amount = $moneyParser->parse($line[4], new Currency($line[5]));

        return new Transaction($date, $userId, $userType, $operationType, $amount);
    }
}
