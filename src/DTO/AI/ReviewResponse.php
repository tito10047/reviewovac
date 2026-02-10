<?php

namespace App\DTO\AI;

use App\Enum\ReviewSentiment;
use Symfony\AI\Platform\Contract\JsonSchema\Attribute\With;

/**
 * Facilitates communication with the Symfony AI bundle; the AI returns an instance of this class.
 */
class ReviewResponse
{
    /**
     * @param list<ReviewTranslationResponse> $translations
     */
    public function __construct(
        #[With(enum: [
            ReviewSentiment::Negative->value,
            ReviewSentiment::Positive->value,
            ReviewSentiment::Neutral->value,
        ])] public string $sentiment,
        public ?bool $isProductIssue,
        public array $translations,
    ) {
    }

    public function getSentimentEnum(): ReviewSentiment
    {
        return ReviewSentiment::from($this->sentiment);
    }
}
