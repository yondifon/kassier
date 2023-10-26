<?php

namespace Malico\Kassier;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Malico\Kassier\Database\Factories\PriceFactory;

class Price extends Model
{
    use HasFactory;
    use HasUlids;

    public $fillable = [
        'name',
        'description',
        'currency',
        'price',
        'period',
    ];

    public $casts = [
        'interval' => Interval::class,
        'interval_count' => 'int',
    ];

    protected static function newFactory(): Factory
    {
        return PriceFactory::new();
    }

    public function period(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes): Period {
                return Period::make(Interval::from($attributes['interval']), $attributes['interval_count']);
            },
            set: fn (Period $value): array => [
                'interval' => $value->interval,
                'interval_count' => $value->intervalCount,
            ],
        );
    }
}
