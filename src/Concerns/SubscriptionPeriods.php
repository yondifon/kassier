<?php

namespace Malico\Kassier\Concerns;

use Malico\Kassier\Period;

trait SubscriptionPeriods
{
    public function currentBillingPeriod(): Period
    {
        return $this->price->period->endingOn($this->ends_at->toImmutable());
    }

    public function nextBillingPeriod(): Period
    {
        return $this->price->period->startingOn($this->ends_at->toImmutable());
    }
}
