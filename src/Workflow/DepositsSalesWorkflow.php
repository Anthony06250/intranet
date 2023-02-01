<?php

namespace App\Workflow;

use App\Entity\DepositsSales;
use LogicException;
use Symfony\Component\Workflow\WorkflowInterface;

final class DepositsSalesWorkflow
{
    public const TRANSITION_SOLD = 'sold';
    public const TRANSITION_PAID = 'paid';
    public const TRANSITION_RECOVER = 'recover';

    /**
     * @param WorkflowInterface $depositsSalesStateMachine
     */
    public function __construct(private readonly WorkflowInterface $depositsSalesStateMachine)
    {
    }

    /**
     * @param DepositsSales $depositsSales
     * @return bool
     */
    public function canSold(DepositsSales $depositsSales): bool
    {
        return $this->depositsSalesStateMachine->can($depositsSales, self::TRANSITION_SOLD);
    }

    /**
     * @param DepositsSales $depositsSales
     * @return void
     */
    public function sold(DepositsSales $depositsSales): void
    {
        if (!$this->canSold($depositsSales)) {
            throw new LogicException("Can't apply the 'sold' transition on user n°{$depositsSales->getId()}°, current state: '{$depositsSales->getStatus()}'.");
        }

        $this->depositsSalesStateMachine->apply($depositsSales, self::TRANSITION_SOLD);
    }

    /**
     * @param DepositsSales $depositsSales
     * @return bool
     */
    public function canPaid(DepositsSales $depositsSales): bool
    {
        return $this->depositsSalesStateMachine->can($depositsSales, self::TRANSITION_PAID);
    }

    /**
     * @param DepositsSales $depositsSales
     * @return void
     */
    public function paid(DepositsSales $depositsSales): void
    {
        if (!$this->canPaid($depositsSales)) {
            throw new LogicException("Can't apply the 'paid' transition on user n°{$depositsSales->getId()}°, current state: '{$depositsSales->getStatus()}'.");
        }

        $this->depositsSalesStateMachine->apply($depositsSales, self::TRANSITION_PAID);
    }

    /**
     * @param DepositsSales $depositsSales
     * @return bool
     */
    public function canRecover(DepositsSales $depositsSales): bool
    {
        return $this->depositsSalesStateMachine->can($depositsSales, self::TRANSITION_RECOVER);
    }

    /**
     * @param DepositsSales $depositsSales
     * @return void
     */
    public function recover(DepositsSales $depositsSales): void
    {
        if (!$this->canRecover($depositsSales)) {
            throw new LogicException("Can't apply the 'recover' transition on user n°{$depositsSales->getId()}°, current state: '{$depositsSales->getStatus()}'.");
        }

        $this->depositsSalesStateMachine->apply($depositsSales, self::TRANSITION_RECOVER);
    }
}
