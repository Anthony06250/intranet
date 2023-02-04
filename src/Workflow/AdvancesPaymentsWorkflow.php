<?php

namespace App\Workflow;

use App\Entity\AdvancesPayments;
use LogicException;
use Symfony\Component\Workflow\WorkflowInterface;

class AdvancesPaymentsWorkflow
{
    public const TRANSITION_EXPIRE = 'expire';
    public const TRANSITION_USE = 'use';

    /**
     * @param WorkflowInterface $advancesPaymentsStateMachine
     */
    public function __construct(private readonly WorkflowInterface $advancesPaymentsStateMachine)
    {
    }

    /**
     * @param AdvancesPayments $advancesPayments
     * @return bool
     */
    public function canExpire(AdvancesPayments $advancesPayments): bool
    {
        return $this->advancesPaymentsStateMachine->can($advancesPayments, self::TRANSITION_EXPIRE);
    }

    /**
     * @param AdvancesPayments $advancesPayments
     * @return void
     */
    public function expire(AdvancesPayments $advancesPayments): void
    {
        if (!$this->canExpire($advancesPayments)) {
            throw new LogicException("Can't apply the 'expire' transition on user n°{$advancesPayments->getId()}°, current state: '{$advancesPayments->getStatus()}'.");
        }

        $this->advancesPaymentsStateMachine->apply($advancesPayments, self::TRANSITION_EXPIRE);
    }

    /**
     * @param AdvancesPayments $advancesPayments
     * @return bool
     */
    public function canUse(AdvancesPayments $advancesPayments): bool
    {
        return $this->advancesPaymentsStateMachine->can($advancesPayments, self::TRANSITION_USE);
    }

    /**
     * @param AdvancesPayments $advancesPayments
     * @return void
     */
    public function use(AdvancesPayments $advancesPayments): void
    {
        if (!$this->canUse($advancesPayments)) {
            throw new LogicException("Can't apply the 'recover' transition on user n°{$advancesPayments->getId()}°, current state: '{$advancesPayments->getStatus()}'.");
        }

        $this->advancesPaymentsStateMachine->apply($advancesPayments, self::TRANSITION_USE);
    }
}
