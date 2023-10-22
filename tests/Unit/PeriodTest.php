<?php

namespace Malico\Kassier\Tests\Unit;

use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use Malico\Kassier\Interval;
use Malico\Kassier\Period;
use PHPUnit\Framework\TestCase;

class PeriodTest extends TestCase
{
    /** @test*/
    public function startsAt_defaults_to_now(): void
    {
        $period = Period::make(Interval::DAY, 1);

        $now = CarbonImmutable::now();

        $this->assertTrue($now->isSameMinute($period->startsAt()));
    }

    /** @test*/
    public function startsAt_can_be_set(): void
    {
        $period = Period::make(Interval::DAY, 1, CarbonImmutable::parse('2021-01-01 00:00:00'));

        $this->assertTrue(CarbonImmutable::parse('2021-01-01 00:00:00')->isSameAs($period->startsAt()));
    }

    /**
     * @test
     *
     * @dataProvider periodProvider
     */
    public function it_generates_a_period_for_(Interval $interval, string $startsAt, string $endsAt): void
    {
        $periodA = Period::make($interval, 1, Carbon::parse($startsAt));
        $periodB = Period::make($interval, 1)->startingOn(Carbon::parse($startsAt));

        $expectedEndsAt = Carbon::parse($endsAt);

        $this->assertTrue($expectedEndsAt->isSameAs($periodA->endsAt()));
        $this->assertTrue($expectedEndsAt->isSameAs($periodB->endsAt()));
    }

    /**
     * @test
     *
     * @dataProvider periodProvider
     */
    public function it_generates_next_period_for(Interval $interval, string $startsAt, string $endsAt): void
    {
        $period = Period::make($interval, 1, Carbon::parse($startsAt));
        $nextPeriod = $period->next();

        $endsAt = Carbon::parse($endsAt);

        $this->assertTrue($endsAt->isSameAs($nextPeriod->startsAt()));
    }

    /**
     * @test
     *
     * @dataProvider periodProvider
     */
    public function it_generates_previous_period_for(Interval $interval, string $startsAt, string $endsAt): void
    {
        $period = Period::make($interval, 1, Carbon::parse($startsAt));
        $previousPeriod = $period->previous();

        $startsAt = Carbon::parse($startsAt);

        $this->assertTrue($startsAt->isSameAs($previousPeriod->endsAt()));
    }

    /**
     * @test
     *
     * @dataProvider periodProvider
     */
    public function can_set_the_quantity_of_the_period(Interval $interval, string $startsAt, string $endsAt): void
    {
        $period = Period::make($interval, 1, Carbon::parse($startsAt));
        $period->quantity(2);

        $expectedEndsAt = Carbon::parse($endsAt)->add($interval->value, $interval->value);

        $this->assertTrue($expectedEndsAt->isSameAs($period->endsAt()));
    }

    public static function periodProvider(): array
    {
        return [
            'day' => [Interval::DAY, '2021-01-01 00:00:00', '2021-01-02 00:00:00'],
            'week' => [Interval::WEEK, '2021-01-01 00:00:00', '2021-01-08 00:00:00'],
            'month' => [Interval::MONTH, '2021-01-01 00:00:00', '2021-02-01 00:00:00'],
            'year' => [Interval::YEAR, '2021-01-01 00:00:00', '2022-01-01 00:00:00'],
        ];
    }
}
