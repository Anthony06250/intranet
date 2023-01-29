<?php

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

final class BuybacksStatusesType extends AbstractEnumType
{
    public const STATE_PENDING = 'pending';
    public const STATE_EXPIRED = 'expired';
    public const STATE_RECOVERED = 'recovered';

    public const TYPES = [
        self::STATE_PENDING,
        self::STATE_EXPIRED,
        self::STATE_RECOVERED
    ];

    /**
     * @var array|string[]
     */
    protected static array $choices = [
        self::STATE_PENDING => 'Buybacks.Statuses.Pending',
        self::STATE_EXPIRED => 'Buybacks.Statuses.Expired',
        self::STATE_RECOVERED => 'Buybacks.Statuses.Recovered'
    ];
}
