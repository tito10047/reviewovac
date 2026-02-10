<?php

namespace App\MessageHandler;

use App\Entity\Review;
use App\Message\ProcessReviewMessage;
use App\Repository\ReviewRepository;
use App\Service\ReviewProcessServiceInterface;
use App\Service\TranslationManager;
use BugCatcher\Reporter\Service\BugCatcherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
class ProcessReviewHandler {

    public function __construct(
        private readonly ReviewProcessServiceInterface $reviewService,
        private readonly ReviewRepository $reviewRepo,
        private readonly TranslationManager $translationManager,
        private readonly EntityManagerInterface $em,
        private readonly BugCatcherInterface $bugCatcher
    ) { }
    public function __invoke(ProcessReviewMessage $message): void {

        $review = $this->reviewRepo->find(Uuid::fromString($message->reviewId));

        if (!$review) {
            //Probably the review was deleted, but we need to log it for a beta version
            $this->bugCatcher->logException(new \Exception("Review not found for ID: {$message->reviewId}"));
            return;
        }

        $response = $this->reviewService->processReview($review);

        $review
            ->setSentiment($response->getSentimentEnum())
            ->setProcessed(true);

        foreach($response->translations as $translation) {
            if (!$review->getPrimaryLanguage()?->needTranslationFor($translation->getLocaleEnum())){
                continue;
            }

            $this->translationManager->upsert(
                object: $review,
                locale: $translation->getLocaleEnum()->value,
                field: Review::CONTENT_PROPERTY,
                value: $translation->text,
                flush: false
            );
        }

        $this->em->flush();
    }

}
