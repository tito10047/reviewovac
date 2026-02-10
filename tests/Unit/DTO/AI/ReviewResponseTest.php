<?php

namespace App\Tests\Unit\DTO\AI;

use App\DTO\AI\ReviewResponse;
use App\Enum\ReviewSentiment;
use PHPUnit\Framework\TestCase;

class ReviewResponseTest extends TestCase
{
    use AssertWithAttributeTrait;

    public function testSentimentAttributeContainsAllEnumValues(): void
    {
        $this->assertWithAttributeContainsAllEnumValues(
            ReviewResponse::class,
            'sentiment',
            ReviewSentiment::class
        );
    }
}
