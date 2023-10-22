<?php

namespace Malico\Kassier;

use Carbon\CarbonImmutable;
use DateTime;
use DateTimeImmutable;

class Period
{
    protected int $quantity = 1;

    protected function __construct(
        public readonly Interval $interval,
        public readonly int $intervalCount,
        protected DateTimeImmutable|DateTime|null $startsAt = null,
    ) {
        //
    }

    public static function make(Interval $interval, int $intervalCount, DateTimeImmutable|DateTime $startsAt = null): self
    {
        return new self($interval, $intervalCount, $startsAt);
    }

    public static function days(int $intervalCount = 1, DateTimeImmutable|DateTime $startsAt = null): self
    {
        return self::make(Interval::DAY, $intervalCount, $startsAt);
    }

    public static function day(DateTimeImmutable|DateTime $startsAt = null): self
    {
        return self::days(1, $startsAt);
    }

    public static function weeks(int $intervalCount = 1, DateTimeImmutable|DateTime $startsAt = null): self
    {
        return self::make(Interval::WEEK, $intervalCount, $startsAt);
    }

    public static function months(int $intervalCount = 1, DateTimeImmutable|DateTime $startsAt = null): self
    {
        return self::make(Interval::MONTH, $intervalCount, $startsAt);
    }

    public static function month(DateTimeImmutable|DateTime $startsAt = null): self
    {
        return self::months(1, $startsAt);
    }

    public static function years(int $intervalCount = 1, DateTimeImmutable|DateTime $startsAt = null): self
    {
        return self::make(Interval::YEAR, $intervalCount, $startsAt);
    }

    public static function year(DateTimeImmutable|DateTime $startsAt = null): self
    {
        return self::years(1, $startsAt);
    }

    /**
     * Get the value of startsAt
     */
    public function startsAt(): CarbonImmutable
    {
        return $this->startsAt ?
            CarbonImmutable::instance($this->startsAt) :
            CarbonImmutable::now();
    }

    /**
     * Get the value of endsAt
     */
    public function endsAt(): CarbonImmutable
    {
        return $this->startsAt()->add($this->interval->value, $this->intervalCount * $this->quantity);
    }

    /**
     *  Get a period that starts after the current one
     */
    public function next(): self
    {
        return self::make($this->interval, $this->intervalCount, $this->endsAt())
            ->quantity($this->quantity);
    }

    /**
     *  Get a period that starts before the current one
     */
    public function previous(): self
    {
        return self::make(
            $this->interval,
            $this->intervalCount,
            $this->startsAt()->sub($this->interval->value, $this->intervalCount * $this->quantity)
        )
            ->quantity($this->quantity);
    }

    /**
     *  Get a period starting on a given date
     */
    public function startingOn(DateTimeImmutable|DateTime $startsAt): self
    {
        return self::make($this->interval, $this->intervalCount, $startsAt)
            ->quantity($this->quantity);
    }

    /**
     *  Get a period ending on a given date
     */
    public function endingOn(DateTimeImmutable|DateTime $endsAt): self
    {
        $endsAt = CarbonImmutable::instance($endsAt);

        return self::make($this->interval, $this->intervalCount, $endsAt->sub($this->interval->value, $this->intervalCount))
            ->quantity($this->quantity);
    }

    public function quantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
