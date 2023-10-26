<?php

namespace Malico\Kassier;

use Illuminate\Database\Eloquent\Relations\HasMany;

trait Billable
{
    /**
     * Create new subscription.
     */
    public function newSubscription(string $name, string|Price $price): SubscriptionBuilder
    {
        return new SubscriptionBuilder($this, $name, $price);
    }

    /**
     * Determine if the Stripe model is on trial.
     */
    public function onTrial(string $name = 'default', string $price = null): bool
    {
        $subscription = $this->subscription($name);

        if (! $subscription || ! $subscription->onTrial()) {
            return false;
        }

        return ! $price || $subscription->hasPrice($price);
    }

    /**
     * Determine if the Stripe model's trial has ended.
     */
    public function hasExpiredTrial(string $name = 'default', string $price = null): bool
    {
        $subscription = $this->subscription($name);

        if (! $subscription || ! $subscription->hasExpiredTrial()) {
            return false;
        }

        return ! $price || $subscription->hasPrice($price);
    }

    /**
     * Determine if the Stripe model has a given subscription.
     */
    public function subscribed(string $name = 'default', string $price = null): bool
    {
        $subscription = $this->subscription($name);

        if (! $subscription || ! $subscription->valid()) {
            return false;
        }

        return ! $price || $subscription->hasPrice($price);
    }

    /**
     * Get a subscription instance by name.
     *
     * @return \Malico\Kassier\Subscription|null
     */
    public function subscription(string $name = 'default')
    {
        return $this->subscriptions
            ->sortByDesc(fn (Subscription $subscription) => $subscription->valid())
            ->where('name', $name)
            ->first();
    }

    /**
     * Get all of the subscriptions for the Stripe model.
     */
    public function subscriptions(): HasMany
    {
        $model = config('kassier.models.subscription');

        return $this->hasMany($model, $this->getForeignKey())->orderBy('created_at', 'desc');
    }

    /**
     * Determine if the customer's subscription has an incomplete payment.
     */
    public function hasIncompletePayment(string $name = 'default'): bool
    {
        if ($subscription = $this->subscription($name)) {
            return $subscription->hasIncompletePayment();
        }

        return false;
    }

    /**
     * Determine if the Stripe model is actively subscribed to one of the given prices.
     *
     * @param  string|string[]  $prices
     */
    public function subscribedToPrice(string|array $prices, string $name = 'default'): bool
    {
        $subscription = $this->subscription($name);

        if (! $subscription || ! $subscription->valid()) {
            return false;
        }

        foreach ((array) $prices as $price) {
            if ($subscription->hasPrice($price)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the customer has a valid subscription on the given price.
     */
    public function onPrice(string $price): bool
    {
        return ! is_null($this->subscriptions->first(function (Subscription $subscription) use ($price) {
            return $subscription->valid() && $subscription->hasPrice($price);
        }));
    }
}
