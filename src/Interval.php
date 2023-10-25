<?php

namespace Malico\Kassier;

enum Interval: string
{
    case DAY = 'day';

    case WEEK = 'week';

    case MONTH = 'month';

    case YEAR = 'year';
}
