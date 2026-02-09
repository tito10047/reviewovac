<?php

namespace App\Service;

use App\DTO\AI\ReviewResponse;
use App\Entity\Review;

interface ReviewProcessServiceInterface {

    /**
     * @throws \Throwable
     */
    public function processReview(Review $review): ReviewResponse;

}
