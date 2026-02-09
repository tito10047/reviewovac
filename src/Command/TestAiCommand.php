<?php

namespace App\Command;

use App\DTO\AI\ReviewResponse;
use App\Repository\ReviewRepository;
use App\Service\ReviewProcessService;
use App\Service\ReviewProcessServiceInterface;
use Symfony\AI\Platform\Message\Message;
use Symfony\AI\Platform\Message\MessageBag;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:test-ai')]
class TestAiCommand {

    public function __construct(
        private readonly ReviewProcessServiceInterface $reviewProcessService,
        private readonly ReviewRepository $reviewRepo,
    ) { }

    public function __invoke() {
        $randomPrompt = $this->reviewRepo->findRandomReview();

        $reviewResponse = $this->reviewProcessService->processReview($randomPrompt);

        dd($reviewResponse);
    }
}
