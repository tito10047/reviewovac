<?php

namespace App\Command;

use App\Message\ProcessReviewMessage;
use App\Repository\ReviewRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(name: 'app:process-new-reviews')]
class ProcessNewReviewsCommand {

    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly ReviewRepository $reviewRepo,
    ) { }

    public function __invoke(SymfonyStyle $io): int {
        $reviewIds = $this->reviewRepo->findUnprocessedIds();

        $progressBar = $io->createProgressBar(count($reviewIds));

        foreach($reviewIds as $reviewId) {
            $progressBar->advance();
            $this->bus->dispatch(new ProcessReviewMessage($reviewId->toString()));
        }

        $progressBar->finish();
        $io->newLine();

        return Command::SUCCESS;
    }
}
