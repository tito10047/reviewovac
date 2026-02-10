<?php

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
class ProcessReviewMessage
{
    public function __construct(
        public readonly string $reviewId,
    ) {
    }
}
