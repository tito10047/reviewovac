<?php

namespace App\Tests\Integration\Service;

use App\Entity\Review;
use App\Service\ReviewProcessService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ReviewProcessServiceTest extends KernelTestCase
{
    public function testProcessReview(): void
    {
        $realAi = $_ENV['REAL_AI'] ?? $_SERVER['REAL_AI'] ?? getenv('REAL_AI');
        if (!$realAi) {
            $this->markTestSkipped('Test skipped because REAL_AI environment variable is not set.');
        }

        self::bootKernel();
        $container = static::getContainer();

        /** @var ReviewProcessService $service */
        $service = $container->get(ReviewProcessService::class);

        $review = new Review();
        $review->setContent('Tento produkt je skvelý, som veľmi spokojný!');

        $response = $service->processReview($review);

        $this->assertNotNull($response);
        $this->assertNotEmpty($response->sentiment);
        $this->assertIsBool($response->isProductIssue);
    }
}
