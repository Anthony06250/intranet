<?php

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

final class DepositsSalesStatusesType extends AbstractEnumType
{
    public const STATE_PENDING = 'pending';
    public const STATE_SOLDED = 'solded';
    public const STATE_PAYED = 'payed';
    public const STATE_RECOVERED = 'recovered';

    public const TYPES = [
        self::STATE_PENDING,
        self::STATE_SOLDED,
        self::STATE_PAYED,
        self::STATE_RECOVERED
    ];

    /**
     * @var array|string[]
     */
    protected static array $choices = [
        self::STATE_PENDING => 'DepositsSales.Statuses.Pending',
        self::STATE_SOLDED => 'DepositsSales.Statuses.Solded',
        self::STATE_PAYED => 'DepositsSales.Statuses.Payed',
        self::STATE_RECOVERED => 'DepositsSales.Statuses.Recovered'
    ];
}
