<?php

namespace App\Workflow;

use App\DBAL\Types\BuybacksStatusesType;
use App\Entity\Buybacks;
use LogicException;
use Symfony\Component\Workflow\WorkflowInterface;

final class BuybacksWorkflow
{
    public const TRANSITION_EXPIRE = 'expire';
    public const TRANSITION_RECOVER = 'recover';

    /**
     * @param WorkflowInterface $buybacksStateMachine
     */
    public function __construct(private readonly WorkflowInterface $buybacksStateMachine)
    {
    }

    /**
     * @param Buybacks $buyback
     * @return bool
     */
    public function isExpire(Buybacks $buyback): bool
    {
        if (array_key_exists(BuybacksStatusesType::STATE_EXPIRED, $this->buybacksStateMachine->getMarking($buyback)->getPlaces())
            && $this->buybacksStateMachine->getMarking($buyback)->getPlaces()[BuybacksStatusesType::STATE_EXPIRED] === 1) {
            return true;
        }

        return false;
    }

    /**
     * @param Buybacks $buyback
     * @return bool
     */
    public function canExpire(Buybacks $buyback): bool
    {
        return $this->buybacksStateMachine->can($buyback, self::TRANSITION_EXPIRE);
    }

    /**
     * @param Buybacks $buyback
     * @return void
     */
    public function expire(Buybacks $buyback): void
    {
        if (!$this->canExpire($buyback)) {
            throw new LogicException("Can't apply the 'expire' transition on user n°{$buyback->getId()}°, current state: '{$buyback->getStatus()}'.");
        }

        $this->buybacksStateMachine->apply($buyback, self::TRANSITION_EXPIRE);
    }

    /**
     * @param Buybacks $buyback
     * @return bool
     */
    public function canRecover(Buybacks $buyback): bool
    {
        return $this->buybacksStateMachine->can($buyback, self::TRANSITION_RECOVER);
    }

    /**
     * @param Buybacks $buyback
     * @return void
     */
    public function recover(Buybacks $buyback): void
    {
        if (!$this->canRecover($buyback)) {
            throw new LogicException("Can't apply the 'recover' transition on user n°{$buyback->getId()}°, current state: '{$buyback->getStatus()}'.");
        }

        $this->buybacksStateMachine->apply($buyback, self::TRANSITION_RECOVER);
    }
}
