<?php

namespace Malico\Kassier;

enum SubscriptionStatus: string
{
    case ACTIVE = 'active';

    case CANCELLED = 'cancelled';

    case INCOMPLETE = 'incomplete';
}
