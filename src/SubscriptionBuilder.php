<?php

namespace Malico\Kassier;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class SubscriptionBuilder
{
    /**
     * The date and time the trial will expire.
     *
     * @var \Carbon\Carbon|\Carbon\CarbonInterface|null
     */
    protected $trialExpires;

    /**
     * The quantity of the subscription.
     */
    protected int $quantity = 1;

    /**
     * Start date of the subscription.
     *
     * @var \Carbon\Carbon|\Carbon\CarbonInterface
     */
    protected ?Carbon $startsAt = null;

    public function __construct(
        protected Model $owner,
        protected string $name,
        protected string|Price $price,
    ) {
    }

    /**
     * Set the quantity of the subscription.
     */
    public function quantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Set the start date of the subscription.
     */
    public function startsAt(Carbon $startsAt): self
    {
        $this->startsAt = $startsAt;

        return $this;
    }

    public function startingOn(Carbon $startsAt): self
    {
        return $this->startsAt($startsAt);
    }

    public function starting(Carbon $startsAt): self
    {
        return $this->startsAt($startsAt);
    }

    /**
     * Set the trial expiration date of the subscription.
     */
    public function trialUntil(Carbon $trialExpires): self
    {
        $this->trialExpires = $trialExpires;

        return $this;
    }

    public function price(string|Price $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function create(): Subscription
    {
        $price = $this->price instanceof Price ? $this->price : Price::find($this->price);
        $period = $price->period
            ->startingOn($this->startsAt ?? Carbon::now())
            ->quantity($this->quantity);

        $subscription = $this->owner->subscriptions()->create([
            'name' => $this->name,
            'price_id' => $this->price->id,
            'status' => SubscriptionStatus::ACTIVE,
            'quantity' => $this->quantity,
            'starts_at' => $period->startsAt(),
            'ends_at' => $period->endsAt(),
            'trial_ends_at' => $this->trialExpires,
        ]);

        return $subscription;
    }
}
