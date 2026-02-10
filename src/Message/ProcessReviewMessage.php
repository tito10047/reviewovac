<?php

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

/**
 * Dispatches a review entity for sentiment analysis and translation into all supported languages.
 */
#[AsMessage('async')]
class ProcessReviewMessage
{
    public function __construct(
        public readonly string $reviewId,
    ) {
    }
}
