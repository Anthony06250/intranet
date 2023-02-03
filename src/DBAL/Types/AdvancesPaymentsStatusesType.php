<?php

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class AdvancesPaymentsStatusesType extends AbstractEnumType
{
    public const STATE_PENDING = 'pending';
    public const STATE_EXPIRED = 'expired';
    public const STATE_USED = 'used';

    public const TYPES = [
        self::STATE_PENDING,
        self::STATE_EXPIRED,
        self::STATE_USED
    ];

    /**
     * @var array|string[]
     */
    protected static array $choices = [
        self::STATE_PENDING => 'AdvancesPayments.Statuses.Pending',
        self::STATE_EXPIRED => 'AdvancesPayments.Statuses.Expired',
        self::STATE_USED => 'AdvancesPayments.Statuses.Used'
    ];
}
