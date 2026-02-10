<?php

namespace App\Tests\Integration\Command;

use App\Entity\Review;
use App\Message\ProcessReviewMessage;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;

class ProcessNewReviewsCommandTest extends KernelTestCase
{
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
        // 1. Vyčistenie a príprava dát
        // V integračných testoch predpokladáme, že máme čistú testovaciu databázu alebo používame transakcie.
        foreach ($this->reviewRepository->findAll() as $r) {
            $this->entityManager->remove($r);
        }
        $this->entityManager->flush();

        $review1 = new Review();
        $review1->setProductId(1);
        $review1->setProcessed(false);
        $this->entityManager->persist($review1);

        $review2 = new Review();
        $review2->setProductId(2);
        $review2->setProcessed(false);
        $this->entityManager->persist($review2);

        $review3 = new Review();
        $review3->setProductId(3);
        $review3->setProcessed(true); // Tento by mal byť ignorovaný
        $this->entityManager->persist($review3);

        $this->entityManager->flush();

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
