<?php

declare(strict_types=1);

namespace CommissionGenerator\Service\IOHelpers;

use Money\Money;

/**
 * Interface Output.
 */
interface Output
{
    /**
     * @return mixed
     */
    public function output(Money $data);
}
