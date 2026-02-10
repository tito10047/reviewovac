<?php

namespace App\Enum;

enum ReviewStar: int
{
    case One = 1;
    case Two = 2;
    case Three = 3;
    case Four = 4;
    case Five = 5;

    public function isSmallerThan(self $other): bool
    {
        return $this->value < $other->value;
    }

    public function isGreaterThan(self $other): bool
    {
        return $this->value > $other->value;
    }

    public function isLowest(): bool
    {
        return self::One === $this;
    }

    public function isHighest(): bool
    {
        return self::Five === $this;
    }
}
