<?php

namespace App\Command;

use App\Message\ProcessReviewMessage;
use App\Repository\ReviewRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(name: 'app:test-ai')]
class TestAiCommand
{
    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly ReviewRepository $reviewRepo,
    ) {
    }

    public function __invoke(): int
    {
        $randomPrompt = $this->reviewRepo->findRandomReview();

        if (!$randomPrompt) {
            throw new \Exception('No random review found');
        }

        $id = $randomPrompt->getId();
        if (!$id) {
            throw new \Exception('Review ID is null');
        }

        $this->bus->dispatch(new ProcessReviewMessage($id->toString()));

        return Command::SUCCESS;
    }
}
