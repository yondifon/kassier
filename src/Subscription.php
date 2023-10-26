<?php

namespace Malico\Kassier;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Malico\Kassier\Concerns\SubscriptionPeriods;
use Malico\Kassier\Database\Factories\SubscriptionFactory;

class Subscription extends Model
{
    use HasFactory;
    use SubscriptionPeriods;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public static function newFactory(): Factory
    {
        return SubscriptionFactory::new();
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'status' => SubscriptionStatus::class,
    ];

    /**
     * Get the user that owns the subscription.
     */
    public function user(): BelongsTo
    {
        return $this->owner();
    }

    /**
     * Get the model related to the subscription.
     */
    public function owner(): BelongsTo
    {
        $model = config('kassier.models.customer');

        return $this->belongsTo($model, (new $model)->getForeignKey());
    }

    public function price(): BelongsTo
    {
        $model = config('kassier.models.price');

        return $this->belongsTo($model, (new $model)->getForeignKey());
    }

    /**
     * Determine if the subscription has a specific price.
     */
    public function hasPrice(string $price): bool
    {
        return $this->price_id === $price;
    }

    /**
     * Determine if the subscription is active, on trial, or within its grace period.
     */
    public function valid(): bool
    {
        return $this->active() || $this->onTrial() || $this->onGracePeriod();
    }

    /**
     * Determine if the subscription is active.
     */
    public function active(): bool
    {
        return $this->started() && ! $this->ended();
    }

    /**
     * Filter query by active.
     */
    public function scopeActive(Builder $query): void
    {
        $now = Carbon::now();

        $query->where('starts_at', '<=', $now)
            ->where('ends_at', '>', $now);
    }

    /**
     * Determine if the subscription is no longer active.
     */
    public function canceled(): bool
    {
        return $this->ends_at->isPast() && $this->status === SubscriptionStatus::CANCELLED;
    }

    public function cancelled(): bool
    {
        return $this->canceled();
    }

    /**
     * Filter query by canceled.
     */
    public function scopeCanceled(Builder $query): void
    {
        $query->where('ends_at', '<=', Carbon::now());
    }

    public function scopeCancelled(Builder $query): void
    {
        $this->scopeCanceled($query);
    }

    /**
     * Filter query by not canceled.
     */
    public function scopeNotCanceled(Builder $query): void
    {
        $query->where('ends_at', '>', Carbon::now());
    }

    /**
     * Filter query by not cancelled.
     */
    public function scopeNotCancelled(Builder $query): void
    {
        $this->scopeNotCanceled($query);
    }

    /**
     * Determine if has started yet.
     */
    public function started(): bool
    {
        return $this->starts_at->isPast();
    }

    /**
     * Filter query by started.
     */
    public function scopeStarted(Builder $query): void
    {
        $query->where('starts_at', '<=', Carbon::now());
    }

    /**
     * Filter query by not started.
     */
    public function scopeNotStarted(Builder $query): void
    {
        $query->where('starts_at', '>', Carbon::now());
    }

    public function ended(): bool
    {
        return $this->canceled() && ! $this->onGracePeriod();
    }

    public function scopeEnded(Builder $query)
    {
        $query->canceled()->notOnGracePeriod();
    }

    /**
     * Determine if the subscription is past due or expired
     */
    public function expired(): bool
    {
        return $this->ends_at->isPast() && $this->status === SubscriptionStatus::ACTIVE;
    }

    /**
     * Filter query by expired.
     */
    public function scopeExpired(Builder $query): void
    {
        $query->where('ends_at', '<=', Carbon::now())
            ->where('status', SubscriptionStatus::ACTIVE);
    }

    /**
     * Determine if the subscription is within its trial period.
     */
    public function onTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Determine if the subscription's trial has expired.
     */
    public function hasExpiredTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isPast();
    }

    /**
     * Filter query by expired trial.
     */
    public function scopeExpiredTrial(Builder $query): void
    {
        $query->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '<', Carbon::now());
    }

    /**
     * Filter query by on trial.
     */
    public function scopeOnTrial(Builder $query): void
    {
        $query->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '>', Carbon::now());
    }

    /**
     * Determine if the subscription is within its grace period after cancellation.
     */
    public function onGracePeriod(): bool
    {
        return $this->ends_at->isFuture() && $this->status === SubscriptionStatus::CANCELLED;
    }

    /**
     * Filter query by on grace period.
     */
    public function scopeOnGracePeriod(Builder $query): void
    {
        $query->where('status', SubscriptionStatus::CANCELLED)
            ->where('ends_at', '>', Carbon::now());
    }

    /**
     * Filter query by not on grace period.
     */
    public function scopeNotOnGracePeriod(Builder $query): void
    {
        $query->where('status', SubscriptionStatus::ACTIVE)
            ->where('ends_at', '<=', Carbon::now());
    }
}
