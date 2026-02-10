<?php

namespace App\Tests\Unit\DTO\AI;

use App\DTO\AI\ReviewTranslationResponse;
use App\Enum\Language;
use PHPUnit\Framework\TestCase;

class ReviewTranslationResponseTest extends TestCase
{
    use AssertWithAttributeTrait;

    public function testLocaleAttributeContainsAllEnumValues(): void
    {
        $this->assertWithAttributeContainsAllEnumValues(
            ReviewTranslationResponse::class,
            'locale',
            Language::class
        );
    }
}
