<?php

namespace App\Command;

use App\Message\ProcessReviewMessage;
use App\Repository\ReviewRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

/**
 * Processes all new review requests.
 */
#[AsCommand(
    name: 'app:process-new-reviews',
    description: 'Processes all new review requests.',
)]
class ProcessNewReviewsCommand
{
    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly ReviewRepository $reviewRepo,
    ) {
    }

    public function __invoke(SymfonyStyle $io): int
    {
        $reviewIdsQB = $this->reviewRepo->findUnprocessedIdsQB();

        $count = (clone $reviewIdsQB)->select('COUNT(r.id)')
            ->getQuery()
            ->getSingleScalarResult();
        assert(is_int($count));
        $progressBar = $io->createProgressBar(
            $count
        );

        /** @var array{id: Uuid} $reviewId */
        foreach ($reviewIdsQB->getQuery()->toIterable() as $reviewId) {
            $progressBar->advance();
            $this->bus->dispatch(new ProcessReviewMessage($reviewId['id']->toString()));
        }

        $progressBar->finish();
        $io->newLine();

        return Command::SUCCESS;
    }
}
