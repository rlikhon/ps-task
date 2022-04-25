<?php

declare(strict_types=1);

namespace CommissionGenerator\Service\IOHelpers;

/**
 * Interface InputByLine.
 */
interface InputByLine
{
    public function getLine(): iterable;
}
