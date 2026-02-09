<?php

namespace App\Command;

use App\DTO\AI\ReviewResponse;
use App\Repository\ReviewRepository;
use Symfony\AI\Agent\AgentInterface;
use Symfony\AI\Platform\Message\Message;
use Symfony\AI\Platform\Message\MessageBag;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(name: 'app:test-ai')]
class TestAiCommand {

    public function __construct(
        #[Autowire(service: 'ai.agent.review_assistant')]
        private readonly AgentInterface $agent,
        private readonly ReviewRepository $reviewRepo,
    ) { }

    public function __invoke() {
        $randomPrompt = $this->reviewRepo->findRandomReview();

        $response = $this->agent->call(new MessageBag(
            Message::ofUser("Review: " . $randomPrompt->getContent()),
        ),[
            'response_format' => ReviewResponse::class
        ]);

        /** @var ReviewResponse $reviewResponse */
        $reviewResponse = $response->getContent();
    }
}
