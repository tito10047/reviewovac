<?php

namespace App\Enum;

enum ReviewSentiment: string
{
    case Positive = 'positive';
    case Negative = 'negative';
    case Neutral = 'neutral';
}
