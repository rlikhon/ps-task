<?php

declare(strict_types=1);

namespace CommissionGenerator\App\Models;

use Carbon\Carbon;
use Money\Money;

/**
 * Class Transaction.
 */
class Transaction
{
    /**
     * @var Carbon
     */
    private $date;
    /**
     * @var int
     */
    private $userId;
    /**
     * @var string
     */
    private $userType;
    /**
     * @var string
     */
    private $operationType;
    /**
     * @var Money
     */
    private $amount;

    /**
     * Transaction constructor.
     */
    public function __construct(Carbon $date, int $userId, string $userType, string $operationType, Money $amount)
    {
        $this->date = $date;
        $this->userId = $userId;
        $this->userType = $userType;
        $this->operationType = $operationType;
        $this->amount = $amount;
    }

    public function getDate(): Carbon
    {
        return $this->date;
    }

    public function setDate(Carbon $date): void
    {
        $this->date = $date;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getUserType(): string
    {
        return $this->userType;
    }

    public function setUserType(string $userType): void
    {
        $this->userType = $userType;
    }

    public function getOperationType(): string
    {
        return $this->operationType;
    }

    public function setOperationType(string $operationType): void
    {
        $this->operationType = $operationType;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function setAmount(Money $amount): void
    {
        $this->amount = $amount;
    }
}
