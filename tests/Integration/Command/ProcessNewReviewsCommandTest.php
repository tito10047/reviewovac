<?php

namespace App\Tests\Integration\Command;

use App\Factory\ReviewFactory;
use App\Message\ProcessReviewMessage;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;
use Zenstruck\Foundry\Test\Factories;

class ProcessNewReviewsCommandTest extends KernelTestCase
{
    use Factories;

    private EntityManagerInterface $entityManager;
    private ReviewRepository $reviewRepository;

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
    }

    public function testExecuteDispatchesMessagesForUnprocessedReviews(): void
    {
        // 1. Príprava dát pomocou factory
        $review1 = ReviewFactory::createOne(['productId' => 1, 'processed' => false]);
        $review2 = ReviewFactory::createOne(['productId' => 2, 'processed' => false]);
        $review3 = ReviewFactory::createOne(['productId' => 3, 'processed' => true]);

        // 2. Spustenie príkazu
        $kernel = self::$kernel;
        if (!$kernel) {
            throw new \LogicException('Kernel is not booted.');
        }
        $application = new Application($kernel);
        $command = $application->find('app:process-new-reviews');
        $commandTester = new CommandTester($command);

        $commandTester->execute([]);

        // 3. Overenie
        $commandTester->assertCommandIsSuccessful();

        /** @var InMemoryTransport $transport */
        $transport = self::getContainer()->get('messenger.transport.async');
        $this->assertCount(2, $transport->getSent());

        $sentMessages = [];
        foreach ($transport->getSent() as $envelope) {
            $message = $envelope->getMessage();
            $this->assertInstanceOf(ProcessReviewMessage::class, $message);
            $sentMessages[] = $message->reviewId;
        }

        $id1 = $review1->getId();
        $id2 = $review2->getId();
        $id3 = $review3->getId();

        $this->assertNotNull($id1);
        $this->assertNotNull($id2);
        $this->assertNotNull($id3);

        $this->assertContains($id1->toString(), $sentMessages);
        $this->assertContains($id2->toString(), $sentMessages);
        $this->assertNotContains($id3->toString(), $sentMessages);
    }
}
