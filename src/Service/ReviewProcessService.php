<?php

namespace App\Service;

use App\DTO\AI\ReviewResponse;
use App\Entity\Review;
use Symfony\AI\Agent\AgentInterface;
use Symfony\AI\Agent\Exception\ExceptionInterface;
use Symfony\AI\Platform\Message\Message;
use Symfony\AI\Platform\Message\MessageBag;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ReviewProcessService implements ReviewProcessServiceInterface {

    public function __construct(
        #[Autowire(service: 'ai.agent.review_assistant')]
        private readonly AgentInterface $agent,
    ) { }

    /**
     * @throws ExceptionInterface
     */
    public function processReview(Review $review): ReviewResponse {
        $response = $this->agent->call(new MessageBag(
            Message::ofUser("Review: " . $review->getContent()),
        ),[
            'response_format' => ReviewResponse::class
        ]);

        /** @var ReviewResponse $reviewResponse */
        $reviewResponse = $response->getContent();

        return $reviewResponse;
    }
}
