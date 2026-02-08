<?php

namespace App\Tests\Enum;

use App\Enum\ReviewStar;
use PHPUnit\Framework\TestCase;

class ReviewStarTest extends TestCase
{
    public function testIsSmallerThan(): void
    {
        $this->assertTrue(ReviewStar::One->isSmallerThan(ReviewStar::Two));
        $this->assertTrue(ReviewStar::One->isSmallerThan(ReviewStar::Five));
        $this->assertFalse(ReviewStar::Two->isSmallerThan(ReviewStar::One));
        $this->assertFalse(ReviewStar::Three->isSmallerThan(ReviewStar::Three));
    }

    public function testIsGreaterThan(): void
    {
        $this->assertTrue(ReviewStar::Two->isGreaterThan(ReviewStar::One));
        $this->assertTrue(ReviewStar::Five->isGreaterThan(ReviewStar::One));
        $this->assertFalse(ReviewStar::One->isGreaterThan(ReviewStar::Two));
        $this->assertFalse(ReviewStar::Three->isGreaterThan(ReviewStar::Three));
    }

    public function testIsLowest(): void
    {
        $this->assertTrue(ReviewStar::One->isLowest());
        $this->assertFalse(ReviewStar::Two->isLowest());
        $this->assertFalse(ReviewStar::Five->isLowest());
    }

    public function testIsHighest(): void
    {
        $this->assertTrue(ReviewStar::Five->isHighest());
        $this->assertFalse(ReviewStar::Four->isHighest());
        $this->assertFalse(ReviewStar::One->isHighest());
    }
}
