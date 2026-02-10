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

        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->reviewRepository = $container->get(ReviewRepository::class);
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
        $application = new Application(self::$kernel);
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

        $this->assertContains($review1->getId()->toString(), $sentMessages);
        $this->assertContains($review2->getId()->toString(), $sentMessages);
        $this->assertNotContains($review3->getId()->toString(), $sentMessages);
    }
}
