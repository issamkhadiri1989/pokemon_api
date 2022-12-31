<?php

namespace App\Enum;

/**
 * This enum represents the full list of possible states of a duel.
 */
enum DuelStates: string
{
    case STARTED = 'started';

    case OPENED = 'opened';

    case SUSPENDED = 'suspended';

    case CANCELED = 'canceled';

    case ENDED = 'ended';
}