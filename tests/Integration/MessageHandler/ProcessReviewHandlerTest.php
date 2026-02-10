<?php

namespace App\Tests\Integration\MessageHandler;

use App\DTO\AI\ReviewResponse;
use App\DTO\AI\ReviewTranslationResponse;
use App\Entity\Review;
use App\Enum\Language;
use App\Enum\ReviewSentiment;
use App\Factory\ReviewFactory;
use App\Message\ProcessReviewMessage;
use App\MessageHandler\ProcessReviewHandler;
use App\Repository\ReviewRepository;
use App\Service\ReviewProcessServiceInterface;
use BugCatcher\Reporter\Service\BugCatcherInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Test\Factories;

class ProcessReviewHandlerTest extends KernelTestCase
{
    use Factories;

    private EntityManagerInterface $entityManager;
    private ReviewRepository $reviewRepository;
    private ProcessReviewHandler $handler;
    private ReviewProcessServiceInterface&MockObject $reviewServiceMock;
    private BugCatcherInterface&MockObject $bugCatcherMock;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get(EntityManagerInterface::class);
        $this->entityManager = $entityManager;

        /** @var ReviewRepository $reviewRepository */
        $reviewRepository = $container->get(ReviewRepository::class);
        $this->reviewRepository = $reviewRepository;

        $this->reviewServiceMock = $this->createMock(ReviewProcessServiceInterface::class);
        $container->set(ReviewProcessServiceInterface::class, $this->reviewServiceMock);

        $this->bugCatcherMock = $this->createMock(BugCatcherInterface::class);
        $container->set(BugCatcherInterface::class, $this->bugCatcherMock);

        /** @var ProcessReviewHandler $handler */
        $handler = $container->get(ProcessReviewHandler::class);
        $this->handler = $handler;
    }

    public function testInvokeProcessesReviewSuccessfully(): void
    {
        // 1. Prepare data
        $review = ReviewFactory::createOne([
            'content' => 'Tento produkt je skvelý!',
            'productId' => 123,
            'primaryLanguage' => Language::Slovak,
        ]);
        $reviewId = $review->getId();

        $translationResponse = new ReviewTranslationResponse(
            Language::Hungarian->value,
            'Ez a termék nagyszerű!'
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
                $id = $r->getId();

                return null !== $id && null !== $reviewId && $id->equals($reviewId);
            }))
            ->willReturn($reviewResponse);

        // 3. Execute handler
        $this->assertNotNull($reviewId);
        $message = new ProcessReviewMessage($reviewId->toRfc4122());
        ($this->handler)($message);

        // 4. Verify
        $this->entityManager->clear();
        $updatedReview = $this->reviewRepository->find($reviewId);

        $this->assertNotNull($updatedReview);
        $this->assertEquals(ReviewSentiment::Positive, $updatedReview->getSentiment());
        $this->assertTrue($updatedReview->isProcessed());
        $this->assertEquals('Tento produkt je skvelý!', $updatedReview->getContent());

        // Verify translation was saved in DB
        $translation = $this->entityManager->getRepository(\App\Entity\Translation::class)->findOneBy([
            'objectType' => 'product',
            'objectId' => $reviewId->toRfc4122(),
            'locale' => Language::Hungarian->value,
            'field' => Review::CONTENT_PROPERTY,
        ]);

        $this->assertNotNull($translation);
        $this->assertEquals('Ez a termék nagyszerű!', $translation->value);
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
        $review = ReviewFactory::createOne([
            'content' => 'Tento produkt je fajn',
            'productId' => 456,
            'primaryLanguage' => Language::Slovak,
        ]);

        $reviewId = $review->getId();

        // Translation for Czech when primary is Slovak (should return false in needTranslationFor)
        $translationResponse = new ReviewTranslationResponse(
            Language::Czech->value,
            'Tento produkt je fajn (cz)'
        );

        $reviewResponse = new ReviewResponse(
            ReviewSentiment::Neutral->value,
            false,
            [$translationResponse]
        );

        $this->reviewServiceMock->method('processReview')->willReturn($reviewResponse);

        $this->assertNotNull($reviewId);
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
