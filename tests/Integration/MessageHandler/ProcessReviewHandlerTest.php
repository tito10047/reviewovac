<?php

namespace App\Tests\Integration\MessageHandler;

use App\Entity\Review;
use App\Enum\Language;
use App\Enum\ReviewSentiment;
use App\Message\ProcessReviewMessage;
use App\MessageHandler\ProcessReviewHandler;
use App\Repository\ReviewRepository;
use App\Service\ReviewProcessServiceInterface;
use App\Service\TranslationManager;
use App\DTO\AI\ReviewResponse;
use App\DTO\AI\ReviewTranslationResponse;
use BugCatcher\Reporter\Service\BugCatcherInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class ProcessReviewHandlerTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private ReviewRepository $reviewRepository;
    private ProcessReviewHandler $handler;
    private ReviewProcessServiceInterface&MockObject $reviewServiceMock;
    private TranslationManager $translationManager;
    private BugCatcherInterface&MockObject $bugCatcherMock;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->reviewRepository = $container->get(ReviewRepository::class);

        $this->reviewServiceMock = $this->createMock(ReviewProcessServiceInterface::class);
        $this->bugCatcherMock = $this->createMock(BugCatcherInterface::class);

        $this->translationManager = new TranslationManager($this->entityManager);

        $this->handler = new ProcessReviewHandler(
            $this->reviewServiceMock,
            $this->reviewRepository,
            $this->translationManager,
            $this->entityManager,
            $this->bugCatcherMock
        );
    }

    public function testInvokeProcessesReviewSuccessfully(): void
    {
        // 1. Prepare data
        $review = new Review();
        $review->setContent('This is a great product!');
        $review->setProductId(123);
        $review->setPrimaryLanguage(Language::Slovak);
        $this->entityManager->persist($review);
        $this->entityManager->flush();

        $reviewId = $review->getId();

        $translationResponse = new ReviewTranslationResponse(
            Language::Hungarian->value,
            'Ez egy nagyszerű termék!'
        );

        $reviewResponse = new ReviewResponse(
            ReviewSentiment::Positive->value,
            false,
            [$translationResponse]
        );

        // 2. Set expectations
        $this->reviewServiceMock->expects($this->once())
            ->method('processReview')
            ->with($this->callback(function (Review $r) use ($reviewId) {
                return $r->getId()->equals($reviewId);
            }))
            ->willReturn($reviewResponse);

        // 3. Execute handler
        $message = new ProcessReviewMessage($reviewId->toRfc4122());
        ($this->handler)($message);

        // 4. Verify
        $this->entityManager->clear();
        $updatedReview = $this->reviewRepository->find($reviewId);

        $this->assertNotNull($updatedReview);
        $this->assertEquals(ReviewSentiment::Positive, $updatedReview->getSentiment());
        $this->assertTrue($updatedReview->isProcessed());

        // Verify translation was saved in DB
        $translation = $this->entityManager->getRepository(\App\Entity\Translation::class)->findOneBy([
            'objectType' => 'product',
            'objectId' => $reviewId->toRfc4122(),
            'locale' => Language::Hungarian->value,
            'field' => Review::CONTENT_PROPERTY,
        ]);

        $this->assertNotNull($translation);
        $this->assertEquals('Ez egy nagyszerű termék!', $translation->value);
    }

    public function testInvokeLogsExceptionWhenReviewNotFound(): void
    {
        $nonExistentId = Uuid::v4()->toRfc4122();
        $message = new ProcessReviewMessage($nonExistentId);

        $this->bugCatcherMock->expects($this->once())
            ->method('logException')
            ->with($this->callback(function (\Exception $e) use ($nonExistentId) {
                return str_contains($e->getMessage(), "Review not found for ID: $nonExistentId");
            }));

        ($this->handler)($message);
    }

    public function testInvokeDoesNotTranslateIfNoTranslationNeeded(): void
    {
        // Preparation
        $review = new Review();
        $review->setContent('Test');
        $review->setProductId(456);
        $review->setPrimaryLanguage(Language::Slovak);
        $this->entityManager->persist($review);
        $this->entityManager->flush();

        $reviewId = $review->getId();

        // Translation for Czech when primary is Slovak (should return false in needTranslationFor)
        $translationResponse = new ReviewTranslationResponse(
            Language::Czech->value,
            'Test cz'
        );

        $reviewResponse = new ReviewResponse(
            ReviewSentiment::Neutral->value,
            false,
            [$translationResponse]
        );

        $this->reviewServiceMock->method('processReview')->willReturn($reviewResponse);

        $message = new ProcessReviewMessage($reviewId->toRfc4122());
        ($this->handler)($message);

        // Verify NO translation was saved in DB for Czech
        $translation = $this->entityManager->getRepository(\App\Entity\Translation::class)->findOneBy([
            'objectType' => 'product',
            'objectId' => $reviewId->toRfc4122(),
            'locale' => Language::Czech->value,
            'field' => Review::CONTENT_PROPERTY,
        ]);

        $this->assertNull($translation);
    }
}
