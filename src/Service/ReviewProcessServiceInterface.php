<?php

namespace App\Service;

use App\DTO\AI\ReviewResponse;
use App\Entity\Review;

interface ReviewProcessServiceInterface
{
    /**
     * Analyzes review sentiment and generates translations using AI.
     *
     * @throws \Throwable
     */
    public function processReview(Review $review): ReviewResponse;
}
